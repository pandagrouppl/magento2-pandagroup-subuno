<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Transformer;

use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\Subuno\Api\TransformerInterface;
use PandaGroup\SubunoApi\Contract\DataObjectInterface;
use PandaGroup\SubunoApi\DataObject\BillingInformation as BillingInformationDataObject;
use PandaGroup\SubunoApi\DataObject\Factory\Factory;

class BillingInformation implements TransformerInterface
{
    private Factory $factory;

    public function __construct(Factory $factory)
    {
        $this->factory = $factory;
    }

    public function transform(OrderInterface $order): ?DataObjectInterface
    {
        $billingAddress = $order->getBillingAddress();
        if (empty($billingAddress)) {
            $billingAddress = $order->getShippingAddress();
        }

        $billingStreet = $billingAddress->getStreet();
        return $this->factory->create(BillingInformationDataObject::class, [
            'customerName' => $billingAddress->getName(),
            'phone' => $billingAddress->getTelephone(),
            'email' => $order->getCustomerEmail(),
            'company' => $billingAddress->getCompany(),
            'billStreetOne' => $billingStreet[0] ?? '',
            'billStreetTwo' => $billingStreet[1] ?? '',
            'billCity' => $billingAddress->getCity(),
            'billState' => $billingAddress->getRegion(),
            'billCountry' => $billingAddress->getCountryId(),
            'billZip' => $billingAddress->getPostcode(),
        ]);
    }
}