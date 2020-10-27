<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TabOpened implements SerializablePayload
{
    public string $tabId;
    public int $tableNumber;
    public string $waiter;

    //todo PHP 8 constructor promotion here.
    public function __construct(string $tabId, $tableNumber, $waiter)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
    }

    public static function fromPayload(array $payload) : self
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