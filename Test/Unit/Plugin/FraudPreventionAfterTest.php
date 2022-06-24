<?php declare(strict_types=1);

namespace PandaGroup\Subuno\Test\Unit\Plugin;

use Magento\Framework\TestFramework\Unit\BaseTestCase;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Model\Order;
use PandaGroup\Subuno\Api\Data\SubunoResponseInterface;
use PandaGroup\Subuno\Model\Config;
use PandaGroup\Subuno\Modifier\OrderStatusModifier;
use PandaGroup\Subuno\Plugin\FraudPreventionAfter;
use PandaGroup\Subuno\Service\Comparator\IsActionReject;
use PHPUnit\Framework\MockObject\MockObject;

class FraudPreventionAfterTest extends BaseTestCase
{
    /** @var MockObject|Config */
    private MockObject $configMock;

    /** @var MockObject|OrderPaymentInterface */
    private MockObject $paymentMock;

    /** @var MockObject|OrderInterface */
    private MockObject $orderMock;

    /** @var MockObject|OrderExtensionInterface */
    private MockObject $extensionAttributesMock;

    /** @var MockObject|SubunoResponseInterface */
    private MockObject $subunoResponseMock;

    /** @var MockObject|IsActionReject */
    private MockObject $isActionRejectMock;

    /** @var MockObject|OrderStatusModifier  */
    private MockObject $orderStatusModifierMock;

    /** @var object|FraudPreventionAfter */
    private object $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $arguments = $this->objectManager->getConstructArguments(FraudPreventionAfter::class);
        $this->configMock = $arguments['config'];
        $this->isActionRejectMock = $arguments['isActionReject'];
        $this->orderStatusModifierMock = $arguments['orderStatusModifier'];
        $this->paymentMock = $this->getMockBuilder(OrderPaymentInterface::class)->addMethods(['getOrder'])->getMockForAbstractClass();
        $this->orderMock = $this->getMockBuilder(OrderInterface::class)->addMethods(['addStatusHistoryComment'])->getMockForAbstractClass();
        $this->extensionAttributesMock = $this->getMockBuilder(OrderExtensionInterface::class)->addMethods(['getSubunoResponse'])->getMockForAbstractClass();
        $this->subunoResponseMock = $this->getMockBuilder(SubunoResponseInterface::class)->getMockForAbstractClass();
        $this->subject = $this->objectManager->getObject(FraudPreventionAfter::class, $arguments);
    }

    public function testGivenDisabledModuleConfig_thenAssertOrderStatusIsNotChanged(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(false);
        $this->orderMock->expects($this->never())->method('setState');
        $this->orderMock->expects($this->never())->method('setStatus');
        $this->subject->afterPlace($this->paymentMock, $this->paymentMock);
    }

    public function testGivenRunAsyncConfig_thenAssertOrderStatusIsNotChanged(): void
    {
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(2);
        $this->orderMock->expects($this->never())->method('setState');
        $this->orderMock->expects($this->never())->method('setStatus');
        $this->subject->afterPlace($this->paymentMock, $this->paymentMock);
    }

    public function testGivenEmptySubunoResponse_thenAssertOrderStatusIsNotChanged(): void
    {
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn(null);
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->orderMock->expects($this->never())->method('setState');
        $this->orderMock->expects($this->never())->method('setStatus');
        $this->subject->afterPlace($this->paymentMock, $this->paymentMock);
    }

    public function testGivenSubunoResponseWithManualReviewActionAndConfigOnHold_thenAssertOrderStatusIsNotChanged(): void
    {
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->configMock->method('rejectAction')->willReturn(1);
        $this->subunoResponseMock->method('getAction')->willReturn('manual_review');
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn($this->subunoResponseMock);
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->orderMock->expects($this->never())->method('setState');
        $this->orderMock->expects($this->never())->method('setStatus');
        $this->orderMock->expects($this->never())->method('addStatusHistoryComment');
        $this->subject->afterPlace($this->paymentMock, $this->paymentMock);
    }

    public function testGivenSubunoResponseWithRejectActionAndConfigOnHold_thenAssertOrderStatusIsChangedToOnHold(): void
    {
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->configMock->method('rejectAction')->willReturn(1);
        $this->subunoResponseMock->method('getAction')->willReturn('reject');
        $this->isActionRejectMock->method('execute')->willReturn(true);
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn($this->subunoResponseMock);
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->orderStatusModifierMock->expects($this->once())->method('execute')->with($this->orderMock, Order::STATE_HOLDED, Order::STATUS_FRAUD);
        $this->subject->afterPlace($this->paymentMock, $this->paymentMock);
    }

    public function testGivenSubunoResponseWithRejectActionAndConfigCanceled_thenAssertOrderStatusIsChangedToCanceled(): void
    {
        $this->configMock->method('isEnabled')->willReturn(true);
        $this->configMock->method('whenRun')->willReturn(1);
        $this->configMock->method('rejectAction')->willReturn(2);
        $this->subunoResponseMock->method('getAction')->willReturn('reject');
        $this->isActionRejectMock->method('execute')->willReturn(true);
        $this->extensionAttributesMock->method('getSubunoResponse')->willReturn($this->subunoResponseMock);
        $this->orderMock->method('getExtensionAttributes')->willReturn($this->extensionAttributesMock);
        $this->paymentMock->method('getOrder')->willReturn($this->orderMock);
        $this->orderStatusModifierMock->expects($this->once())->method('execute')->with($this->orderMock, Order::STATE_CANCELED, Order::STATUS_FRAUD);
        $this->subject->afterPlace($this->paymentMock, $this->paymentMock);
    }
}
