<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use PandaGroup\Subuno\Exception\SubunoRejectException;
use PandaGroup\Subuno\Model\Config;
use PandaGroup\Subuno\Service\Comparator\IsActionReject;
use PandaGroup\Subuno\Service\Connector;

class FraudPreventionBefore
{
    private Config $config;
    private Connector $connector;
    private IsActionReject $isActionReject;

    public function __construct(Connector $connector, Config $config, IsActionReject $isActionReject)
    {
        $this->config = $config;
        $this->connector = $connector;
        $this->isActionReject = $isActionReject;
    }

    /**
     * @throws SubunoRejectException
     */
    public function beforePlace(OrderPaymentInterface $subject): array
    {
        $order = $subject->getOrder();
        if (false === $this->shouldExecute($order)) {
            return [];
        }

        $subunoResponse = $this->connector->execute($order);
        $attributes = $order->getExtensionAttributes();
        $attributes->setSubunoResponse($subunoResponse);
        $order->setExtensionAttributes($attributes);

        if ($this->shouldThrownError() && $this->isActionReject->execute($subunoResponse->getAction())) {
            throw new SubunoRejectException(__($this->config->getErrorMessage()));
        }

        return [];
    }

    private function shouldExecute(OrderInterface $order): bool
    {
        return $this->config->isEnabled()
            && $this->config->whenRun() === Config\Source\Run::RUN_DURING_CHECKOUT
            && empty($order->getExtensionAttributes()->getSubunoResponse());
    }

    private function shouldThrownError(): bool
    {
        return $this->config->rejectAction() === Config\Source\Rejects::ORDER_ERROR;
    }
}
