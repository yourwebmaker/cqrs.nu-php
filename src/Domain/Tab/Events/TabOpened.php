<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TabOpened implements SerializablePayload
{
    public function __construct(public string $tabId, public int $tableNumber, public string $waiter)
    {
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['tabId'], $payload['tableNumber'], $payload['waiter']);
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId,
            'tableNumber' => $this->tableNumber,
            'waiter' => $this->waiter,
        ];
    }
}
