<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use PandaGroup\Subuno\Model\Config;
use PandaGroup\Subuno\Modifier\OrderStatusModifier;
use PandaGroup\Subuno\Service\Comparator\IsActionReject;

class FraudPreventionAfter
{
    private IsActionReject $isActionReject;
    private Config $config;
    private OrderStatusModifier $orderStatusModifier;

    public function __construct(IsActionReject $isActionReject, Config $config, OrderStatusModifier $orderStatusModifier)
    {
        $this->isActionReject = $isActionReject;
        $this->config = $config;
        $this->orderStatusModifier = $orderStatusModifier;
    }

    public function afterPlace(OrderPaymentInterface $subject, OrderPaymentInterface $result): OrderPaymentInterface
    {
        $order = $result->getOrder();
        if (!$this->shouldExecute($order)) {
            return $result;
        }

        $subunoResponse = $order->getExtensionAttributes()->getSubunoResponse();
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

        return $result;
    }

    private function shouldExecute(OrderInterface $order): bool
    {
        return $this->config->isEnabled()
            && $this->config->whenRun() === Config\Source\Run::RUN_DURING_CHECKOUT
            && !empty($order->getExtensionAttributes()->getSubunoResponse());
    }
}
