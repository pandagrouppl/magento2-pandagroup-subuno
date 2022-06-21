<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Test\Unit\Transformer;

use Magento\Framework\TestFramework\Unit\BaseTestCase;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\Subuno\Transformer\BillingInformation;
use PandaGroup\Subuno\Transformer\ShippingInformation;
use PandaGroup\SubunoApi\DataObject\Factory\Factory;
use PHPUnit\Framework\MockObject\MockObject;

class ShippingInformationTest extends BaseTestCase
{
    /** @var MockObject|OrderAddressInterface */
    private MockObject $addressMock;

    /** @var MockObject|OrderInterface */
    private MockObject $orderMock;

    private ShippingInformation $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderMock = $this->getMockBuilder(OrderInterface::class)
            ->addMethods(['getShippingAddress'])
            ->getMockForAbstractClass();
        $this->addressMock = $this->getMockBuilder(OrderAddressInterface::class)
            ->addMethods(['getName'])
            ->getMockForAbstractClass();

        $this->subject = new ShippingInformation(new Factory());
    }

    public function testGivenAddressEntity_thenAssertBillingInformationDataObjectIsCorrect(): void
    {
        $this->addressMock->method('getName')->willReturn('Jan Nowak');
        $this->addressMock->method('getTelephone')->willReturn('123456789');
        $this->addressMock->method('getStreet')->willReturn([
            0 => 'Wielka',
            1 => '1',
        ]);
        $this->addressMock->method('getCity')->willReturn('Poznan');
        $this->addressMock->method('getRegion')->willReturn('Wlkp');
        $this->addressMock->method('getCountryId')->willReturn('PL');
        $this->addressMock->method('getPostcode')->willReturn('12-123');
        $this->orderMock->method('getShippingAddress')->willReturn($this->addressMock);
        $this->orderMock->method('getCustomerEmail')->willReturn('jannowak@mail.pl');
        $dataObject = $this->subject->transform($this->orderMock);

        $this->assertEquals('Jan Nowak', $dataObject->get('shipName'));
        $this->assertEquals('123456789', $dataObject->get('shipPhone'));
        $this->assertEquals('jannowak@mail.pl', $dataObject->get('shipEmail'));
        $this->assertEquals('Wielka', $dataObject->get('shipStreetOne'));
        $this->assertEquals('1', $dataObject->get('shipStreetTwo'));
        $this->assertEquals('Poznan', $dataObject->get('shipCity'));
        $this->assertEquals('Wlkp', $dataObject->get('shipState'));
        $this->assertEquals('PL', $dataObject->get('shipCountry'));
        $this->assertEquals('12-123', $dataObject->get('shipZip'));
    }
}
