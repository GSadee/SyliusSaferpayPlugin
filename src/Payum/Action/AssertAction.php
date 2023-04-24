<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusSaferpayPlugin\Payum\Action;

use CommerceWeavers\SyliusSaferpayPlugin\Client\SaferpayClientInterface;
use CommerceWeavers\SyliusSaferpayPlugin\Payum\Request\Assert;
use Payum\Core\Action\ActionInterface;
use Payum\Core\Exception\RequestNotSupportedException;
use Sylius\Component\Core\Model\PaymentInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert as WebmozartAssert;

final class AssertAction implements ActionInterface
{
    public function __construct(private SaferpayClientInterface $saferpayClient)
    {
    }

    /** @param Assert $request */
    public function execute($request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var PaymentInterface $payment */
        $payment = $request->getModel();

        $response = $this->saferpayClient->assert($payment);

        $paymentDetails = $payment->getDetails();
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $paymentDetails['status'] = StatusAction::STATUS_FAILED;

            $error = $response->getError();
            WebmozartAssert::notNull($error);
            $paymentDetails['transaction_id'] = $error->getTransactionId();

            $payment->setDetails($paymentDetails);

            return;
        }

        $transaction = $response->getTransaction();
        WebmozartAssert::notNull($transaction);
        $paymentDetails['status'] = $transaction->getStatus();
        $paymentDetails['transaction_id'] = $transaction->getId();

        $payment->setDetails($paymentDetails);
    }

    public function supports($request): bool
    {
        return ($request instanceof Assert) && ($request->getModel() instanceof PaymentInterface);
    }
}
