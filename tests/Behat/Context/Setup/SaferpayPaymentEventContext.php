<?php

declare(strict_types=1);

namespace Tests\CommerceWeavers\SyliusSaferpayPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use CommerceWeavers\SyliusSaferpayPlugin\Event\SaferpayPaymentEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class SaferpayPaymentEventContext implements Context
{
    public function __construct(
        private MessageBusInterface $commandBus,
    ) {
    }

    /**
     * @Given /^the system has been notified about payment on (this order)$/
     */
    public function theSystemHasBeenNotifiedAboutPaymentOnThisOrder(OrderInterface $order): void
    {
        /** @var PaymentInterface $payment */
        $payment = $order->getPayments()->first();
        /** @var int $paymentId */
        $paymentId = $payment->getId();

        $this->commandBus->dispatch(new SaferpayPaymentEvent(
            new \DateTimeImmutable(),
            $paymentId,
            'Payment authorization',
            [],
        ));

        $this->commandBus->dispatch(new SaferpayPaymentEvent(
            new \DateTimeImmutable(),
            $paymentId,
            'Payment assertion',
            [],
        ));

        $this->commandBus->dispatch(new SaferpayPaymentEvent(
            new \DateTimeImmutable(),
            $paymentId,
            'Payment capture',
            [],
        ));
    }
}
