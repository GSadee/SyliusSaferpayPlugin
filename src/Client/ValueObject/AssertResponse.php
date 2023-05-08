<?php

declare(strict_types=1);

namespace CommerceWeavers\SyliusSaferpayPlugin\Client\ValueObject;

use CommerceWeavers\SyliusSaferpayPlugin\Client\ValueObject\AssertResponse\Error;
use CommerceWeavers\SyliusSaferpayPlugin\Client\ValueObject\AssertResponse\Liability;
use CommerceWeavers\SyliusSaferpayPlugin\Client\ValueObject\AssertResponse\PaymentMeans;
use CommerceWeavers\SyliusSaferpayPlugin\Client\ValueObject\AssertResponse\Transaction;
use CommerceWeavers\SyliusSaferpayPlugin\Client\ValueObject\Header\ResponseHeader;

class AssertResponse
{
    private function __construct(
        private int $statusCode,
        private ResponseHeader $responseHeader,
        private ?Transaction $transaction,
        private ?PaymentMeans $paymentMeans,
        private ?Liability $liability,
        private ?Error $error,
    ) {
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getResponseHeader(): ResponseHeader
    {
        return $this->responseHeader;
    }

    public function getTransaction(): ?Transaction
    {
        return $this->transaction;
    }

    public function getPaymentMeans(): ?PaymentMeans
    {
        return $this->paymentMeans;
    }

    public function getLiability(): ?Liability
    {
        return $this->liability;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['StatusCode'],
            ResponseHeader::fromArray($data['ResponseHeader']),
            isset($data['Transaction']) ? Transaction::fromArray($data['Transaction']) : null,
            isset($data['PaymentMeans']) ? PaymentMeans::fromArray($data['PaymentMeans']) : null,
            isset($data['Liability']) ? Liability::fromArray($data['Liability']) : null,
            isset($data['ErrorName']) ? Error::fromArray($data) : null,
        );
    }
}