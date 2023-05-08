<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TabClosed implements SerializablePayload
{
    public function __construct(
        public string $tabId,
        public float $amountPaid,
        public float $orderValue,
        public float $tipValue,
    ) {
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId,
            'amountPaid' => $this->amountPaid,
            'orderValue' => $this->orderValue,
            'tipValue' => $this->tipValue,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            $payload['tabId'],
            $payload['amountPaid'],
            $payload['orderValue'],
            $payload['tipValue'],
        );
    }
}
