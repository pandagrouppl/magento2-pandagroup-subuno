<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Modifier;

use Magento\Sales\Api\Data\OrderInterface;

class OrderStatusModifier
{
    public function execute(OrderInterface $order, string $state, string $status): void
    {
        $order->setState($state)->setStatus($status)->addStatusHistoryComment("Subuno Fraud Prevention set status of the order to: $status and state to: $state");
    }
}