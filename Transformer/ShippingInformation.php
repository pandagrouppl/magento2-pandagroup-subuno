<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Transformer;

use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\Subuno\Api\TransformerInterface;
use PandaGroup\SubunoApi\Contract\DataObjectInterface;
use PandaGroup\SubunoApi\DataObject\Factory\Factory;
use PandaGroup\SubunoApi\DataObject\ShippingInformation as ShippingInformationDataObject;

class ShippingInformation implements TransformerInterface
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function transform(OrderInterface $order): ?DataObjectInterface
    {
        $shippingAddress = $order->getShippingAddress();
        if (empty($shippingAddress)) {
            return null;
        }

        $shippingStreet = $shippingAddress->getStreet();
        return $this->factory->create(ShippingInformationDataObject::class, [
            'shipName' => $shippingAddress->getName(),
            'shipPhone' => $shippingAddress->getTelephone(),
            'shipEmail' => $order->getCustomerEmail(),
            'shipStreetOne' => $shippingStreet[0] ?? '',
            'shipStreetTwo' => $shippingStreet[1] ?? '',
            'shipCity' => $shippingAddress->getCity(),
            'shipState' => $shippingAddress->getRegion(),
            'shipCountry' => $shippingAddress->getCountryId(),
            'shipZip' => $shippingAddress->getPostcode(),
        ]);
    }
}