<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Test\Unit\Transformer;

use Magento\Framework\TestFramework\Unit\BaseTestCase;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderInterface;
use PandaGroup\Subuno\Model\SecretDataProvider\AVSResponseDataProvider;
use PandaGroup\Subuno\Model\SecretDataProvider\CVVResponseDataProvider;
use PandaGroup\Subuno\Model\SecretDataProvider\IINDataProvider;
use PandaGroup\Subuno\Transformer\OrderInformation;
use PandaGroup\SubunoApi\DataObject\Factory\Factory;
use PHPUnit\Framework\MockObject\MockObject;

class OrderInformationTest extends BaseTestCase
{
    /** @var MockObject|OrderAddressInterface */
    private MockObject $addressMock;

    /** @var MockObject|OrderInterface */
    private MockObject $orderMock;

    /** @var MockObject|CVVResponseDataProvider */
    private MockObject $CVVResponseDataProviderMock;

    /** @var MockObject|IINDataProvider */
    private MockObject $IINDataProviderMock;

    /** @var MockObject|AVSResponseDataProvider */
    private MockObject $AVSResponseDataProviderMock;

    private OrderInformation $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderMock = $this->getMockBuilder(OrderInterface::class)
            ->addMethods(['getShippingAddress'])
            ->getMockForAbstractClass();
        $this->addressMock = $this->getMockBuilder(OrderAddressInterface::class)
            ->addMethods(['getName'])
            ->getMockForAbstractClass();
        $this->CVVResponseDataProviderMock = $this->basicMock(CVVResponseDataProvider::class);
        $this->IINDataProviderMock = $this->basicMock(IINDataProvider::class);
        $this->AVSResponseDataProviderMock = $this->basicMock(AVSResponseDataProvider::class);

        $this->subject = new OrderInformation(new Factory(), $this->CVVResponseDataProviderMock, $this->IINDataProviderMock, $this->AVSResponseDataProviderMock);
    }

    public function testGivenAddressEntity_thenAssertBillingInformationDataObjectIsCorrect(): void
    {
        $this->addressMock->method('getTelephone')->willReturn('123456789');
        $this->orderMock->method('getShippingAddress')->willReturn($this->addressMock);
        $this->orderMock->method('getRemoteIp')->willReturn('127.0.0.1');
        $this->orderMock->method('getIncrementId')->willReturn('10000000');
        $dataObject = $this->subject->transform($this->orderMock);

        $this->assertEquals('123456789', $dataObject->get('issuerPhone'));
        $this->assertEquals('10000000', $dataObject->get('transactionId'));
        $this->assertEquals('127.0.0.1', $dataObject->get('ipAddr'));
    }
}
