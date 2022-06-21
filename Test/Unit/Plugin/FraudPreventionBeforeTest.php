<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Test\Unit\Plugin;

use Magento\Framework\TestFramework\Unit\BaseTestCase;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use PandaGroup\Subuno\Api\Data\SubunoResponseInterface;
use PandaGroup\Subuno\Exception\SubunoRejectException;
use PandaGroup\Subuno\Plugin\FraudPreventionBefore;
use PandaGroup\Subuno\Model\Config;
use PandaGroup\Subuno\Service\Connector;
use PHPUnit\Framework\MockObject\MockObject;

class FraudPreventionBeforeTest extends BaseTestCase
{
    /** @var MockObject|Config */
    private MockObject $configMock;

    /** @var MockObject|Connector */
    private MockObject $connectorMock;

    /** @var MockObject|OrderPaymentInterface */
    private MockObject $paymentMock;

    /** @var MockObject|OrderInterface */
    private MockObject $orderMock;

    /** @var MockObject|OrderExtensionInterface */
    private MockObject $extensionAttributesMock;

    /** @var MockObject|SubunoResponseInterface */
    private MockObject $subunoResponseMock;

    /** @var object|FraudPreventionBefore */
    private object $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $arguments = $this->objectManager->getConstructArguments(FraudPreventionBefore::class);
        $this->configMock = $arguments['config'];
        $this->connectorMock = $arguments['connector'];
        $this->paymentMock = $this->getMockBuilder(OrderPaymentInterface::class)->addMethods(['getOrder'])->getMockForAbstractClass();
        $this->orderMock = $this->getMockBuilder(OrderInterface::class)->addMethods(['addStatusHistoryComment'])->getMockForAbstractClass();
        $this->extensionAttributesMock = $this->getMockBuilder(OrderExtensionInterface::class)->getMockForAbstractClass();
        $this->subunoResponseMock = $this->getMockBuilder(SubunoResponseInterface::class)->getMockForAbstractClass();
        $this->subject = $this->objectManager->getObject(FraudPreventionBefore::class, $arguments);
    }

    public function testGivenDisabledModuleConfig_thenAssertConnectorIsNotExecuted(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(false);
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->connectorMock->expects($this->never())->method('execute');
        $this->subject->beforePlace($this->paymentMock);
    }

    public function testGivenRunAsyncConfig_thenAssertConnectorIsNotExecuted(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(2);
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->connectorMock->expects($this->never())->method('execute');
        $this->subject->beforePlace($this->paymentMock);
    }

    public function testGivenAlreadyLoadedSubunoResponse_thenAssertConnectorIsNotExecuted(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn($this->subunoResponseMock);
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->connectorMock->expects($this->never())->method('execute');
        $this->subject->beforePlace($this->paymentMock);
    }

    public function testGivenShouldExecuteTrueAndConfigOnHoldOnReject_thenAssertConnectorIsExecutedButExceptionIsNotThrown(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->configMock->method('rejectAction')->willReturn(1);
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn(null);
        $this->extensionAttributesMock->method('setSubunoResponse')->willReturnSelf();
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->orderMock->method('setExtensionAttributes')->willReturnSelf();
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->connectorMock->expects($this->once())->method('execute')->willReturn($this->subunoResponseMock);
        $arguments = $this->subject->beforePlace($this->paymentMock);
        $this->assertEquals([], $arguments);
    }

    public function testGivenShouldExecuteTrueAndConfigThrowExceptionOnReject_thenAssertConnectorIsExecutedAndExceptionIsThrown(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->configMock->method('rejectAction')->willReturn(3);
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn(null);
        $this->extensionAttributesMock->method('setSubunoResponse')->willReturnSelf();
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->orderMock->method('setExtensionAttributes')->willReturnSelf();
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->subunoResponseMock->method('getAction')->willReturn('reject');
        $this->connectorMock->expects($this->once())->method('execute')->willReturn($this->subunoResponseMock);
        $this->expectException(SubunoRejectException::class);
        $this->subject->beforePlace($this->paymentMock);
    }

    public function testGivenShouldExecuteTrueAndConfigThrowExceptionOnRejectAndResponseManualReview_thenAssertConnectorIsExecutedAndExceptionIsNotThrown(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->configMock->method('rejectAction')->willReturn(3);
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn(null);
        $this->extensionAttributesMock->method('setSubunoResponse')->willReturnSelf();
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->orderMock->method('setExtensionAttributes')->willReturnSelf();
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->subunoResponseMock->method('getAction')->willReturn('manual_review');
        $this->connectorMock->expects($this->once())->method('execute')->willReturn($this->subunoResponseMock);
        $arguments = $this->subject->beforePlace($this->paymentMock);
        $this->assertEquals([], $arguments);
    }
}
