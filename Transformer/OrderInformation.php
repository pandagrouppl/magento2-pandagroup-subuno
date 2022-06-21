<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Transformer;

use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\Subuno\Api\TransformerInterface;
use PandaGroup\Subuno\Model\SecretDataProvider\AVSResponseDataProvider;
use PandaGroup\Subuno\Model\SecretDataProvider\CVVResponseDataProvider;
use PandaGroup\Subuno\Model\SecretDataProvider\IINDataProvider;
use PandaGroup\SubunoApi\Contract\DataObjectInterface;
use PandaGroup\SubunoApi\DataObject\Factory\Factory;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use PandaGroup\SubunoApi\DataObject\OrderInformation as OrderInformationDataObject;

class OrderInformation implements TransformerInterface
{
    private Factory $factory;
    private CVVResponseDataProvider $CVVResponseDataProvider;
    private IINDataProvider $IINDataProvider;
    private AVSResponseDataProvider $AVSResponseDataProvider;

    public function __construct(
        Factory $factory,
        CVVResponseDataProvider $CVVResponseDataProvider,
        IINDataProvider $IINDataProvider,
        AVSResponseDataProvider $AVSResponseDataProvider
    ) {
        $this->factory = $factory;
        $this->CVVResponseDataProvider = $CVVResponseDataProvider;
        $this->IINDataProvider = $IINDataProvider;
        $this->AVSResponseDataProvider = $AVSResponseDataProvider;
    }

    public function transform(OrderInterface $order): ?DataObjectInterface
    {
        $billingAddress = $order->getBillingAddress();
        if (empty($billingAddress)) {
            $billingAddress = $order->getShippingAddress();
        }

        return $this->factory->create(OrderInformationDataObject::class, [
            'transactionId' => $order->getIncrementId(),
            'ipAddr' => $order->getRemoteIp(),
            'iin' => $this->IINDataProvider->get(),
            'avsResponse' => $this->AVSResponseDataProvider->get(),
            'cvvResponse' => $this->CVVResponseDataProvider->get(),
            'issuerPhone' => $billingAddress->getTelephone(),
        ]);
    }
}