<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Test\Unit\Transformer;

use Magento\Framework\TestFramework\Unit\BaseTestCase;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\Subuno\Transformer\BillingInformation;
use PandaGroup\SubunoApi\DataObject\Factory\Factory;
use PHPUnit\Framework\MockObject\MockObject;

class BillingInformationTest extends BaseTestCase
{
    /** @var MockObject|OrderAddressInterface */
    private MockObject $addressMock;

    /** @var MockObject|OrderInterface */
    private MockObject $orderMock;

    private BillingInformation $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderMock = $this->getMockBuilder(OrderInterface::class)->getMockForAbstractClass();
        $this->addressMock = $this->getMockBuilder(OrderAddressInterface::class)
            ->addMethods(['getName'])
            ->getMockForAbstractClass();

        $this->subject = new BillingInformation(new Factory());
    }

    public function testGivenAddressEntity_thenAssertBillingInformationDataObjectIsCorrect(): void
    {
        $this->addressMock->method('getName')->willReturn('Jan Nowak');
        $this->addressMock->method('getTelephone')->willReturn('123456789');
        $this->addressMock->method('getCompany')->willReturn('Test company');
        $this->addressMock->method('getStreet')->willReturn([
            0 => 'Wielka',
            1 => '1',
        ]);
        $this->addressMock->method('getCity')->willReturn('Poznan');
        $this->addressMock->method('getRegion')->willReturn('Wlkp');
        $this->addressMock->method('getCountryId')->willReturn('PL');
        $this->addressMock->method('getPostcode')->willReturn('12-123');
        $this->orderMock->method('getBillingAddress')->willReturn($this->addressMock);
        $this->orderMock->method('getCustomerEmail')->willReturn('jannowak@mail.pl');
        $dataObject = $this->subject->transform($this->orderMock);

        $this->assertEquals('Jan Nowak', $dataObject->get('customerName'));
        $this->assertEquals('123456789', $dataObject->get('phone'));
        $this->assertEquals('Test company', $dataObject->get('company'));
        $this->assertEquals('Wielka', $dataObject->get('billStreetOne'));
        $this->assertEquals('1', $dataObject->get('billStreetTwo'));
        $this->assertEquals('Poznan', $dataObject->get('billCity'));
        $this->assertEquals('Wlkp', $dataObject->get('billState'));
        $this->assertEquals('PL', $dataObject->get('billCountry'));
        $this->assertEquals('12-123', $dataObject->get('billZip'));
    }
}
