<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\TabId;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TabOpened implements SerializablePayload
{
    public TabId $tabId;
    public int $tableNumber;
    public string $waiter;

    //todo PHP 8 constructor promotion here.
    public function __construct(TabId $tabId, $tableNumber, $waiter)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
    }

    public static function fromPayload(array $payload) : self
    {
        return new self(TabId::fromString($payload['tabId']), $payload['tableNumber'], $payload['waiter']);
    }

    public function toPayload(): array
    {
        return [
            'tableNumber' => $this->tableNumber,
            'waiter' => $this->waiter,
        ];
    }
}