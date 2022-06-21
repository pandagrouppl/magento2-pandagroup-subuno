<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Plugin;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use PandaGroup\Subuno\Api\SubunoResponseRepositoryInterface;

class OrderRepositoryLoadSubunoResponse
{
    private SubunoResponseRepositoryInterface $repository;
    private SearchCriteriaBuilder $searchCriteriaBuilder;

    public function __construct(SearchCriteriaBuilder $searchCriteriaBuilder, SubunoResponseRepositoryInterface $repository)
    {
        $this->repository = $repository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $result): OrderInterface
    {
        $list = $this->repository->getList(
            $this->searchCriteriaBuilder->addFilter('transaction_id', $result->getIncrementId())->create()
        );

        $extensionAttributes = $result->getExtensionAttributes();
        foreach ($list->getItems() as $subunoResponse) {
            $result->setExtensionAttributes($extensionAttributes->setSubunoResponse($subunoResponse));
        }

        return $result;
    }
}