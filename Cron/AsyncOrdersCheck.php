<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Cron;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use PandaGroup\Subuno\Api\Data\SubunoResponseInterface;
use PandaGroup\Subuno\Model\ResourceModel\SubunoResponse\CollectionFactory as SubunoResponseCollectionFactory;
use PandaGroup\Subuno\Model\Config;
use PandaGroup\Subuno\Modifier\OrderStatusModifier;
use PandaGroup\Subuno\Service\Comparator\IsActionReject;
use PandaGroup\Subuno\Service\Connector;

class AsyncOrdersCheck
{
    private Config $config;
    private Connector $connector;
    private SearchCriteriaBuilder $searchCriteriaBuilder;
    private SubunoResponseCollectionFactory $subunoResponseCollectionFactory;
    private OrderRepositoryInterface $orderRepository;
    private IsActionReject $isActionReject;
    private OrderStatusModifier $orderStatusModifier;

    public function __construct(
        Config $config,
        Connector $connector,
        IsActionReject $isActionReject,
        OrderRepositoryInterface $orderRepository,
        OrderStatusModifier $orderStatusModifier,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SubunoResponseCollectionFactory $subunoResponseCollectionFactory
    ) {
        $this->config = $config;
        $this->connector = $connector;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->subunoResponseCollectionFactory = $subunoResponseCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->isActionReject = $isActionReject;
        $this->orderStatusModifier = $orderStatusModifier;
    }

    public function execute(): void
    {
        if ($this->config->whenRun() !== Config\Source\Run::RUN_ASYNC) {
            return;
        }

        $orderIncrementIdsAlreadyChecked = $this->getOrderIncrementIdsAlreadyChecked();
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('increment_id', $orderIncrementIdsAlreadyChecked, 'nin')
            ->setPageSize($this->config->getCronBatchSize())
            ->create();

        $orders = $this->orderRepository->getList($searchCriteria)->getItems();
        foreach ($orders as $order) {
            $subunoResponse = $this->connector->execute($order);
            if (null === $subunoResponse) {
                continue;
            }
            if ($this->isActionReject->execute($subunoResponse->getAction())) {
                switch ($this->config->rejectAction()) {
                    case Config\Source\Rejects::ORDER_ON_HOLD:
                        $this->orderStatusModifier->execute($order, Order::STATE_HOLDED, Order::STATUS_FRAUD);
                        break;
                    case Config\Source\Rejects::ORDER_CANCEL:
                        $this->orderStatusModifier->execute($order, Order::STATE_CANCELED, Order::STATUS_FRAUD);
                        break;
                }
            }

            $attributes = $order->getExtensionAttributes();
            $attributes->setSubunoResponse($subunoResponse);
            $order->setExtensionAttributes($attributes);
            $this->orderRepository->save($order);
        }
    }

    private function getOrderIncrementIdsAlreadyChecked(): array
    {
        $collection = $this->subunoResponseCollectionFactory->create();
        $incrementIds = [];
        /** @var SubunoResponseInterface $subunoResponse */
        foreach ($collection->getItems() as $subunoResponse) {
            $incrementIds[] = $subunoResponse->getTransactionId();
        }

        return $incrementIds;
    }
}