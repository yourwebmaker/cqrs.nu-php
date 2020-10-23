<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\TabId;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class TabClosed implements SerializablePayload
{
    public TabId $tabId; //todo use simple strings for this shit.
    public float $amountPaid;
    public float $orderValue;
    public float $tipValue;

    public function __construct(TabId $tabId, float $amountPaid, float $orderValue, float $tipValue)
    {
        $this->tabId = $tabId;
        $this->amountPaid = $amountPaid;
        $this->orderValue = $orderValue;
        $this->tipValue = $tipValue;
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId->toString(),
            'amountPaid' => $this->amountPaid,
            'orderValue' => $this->orderValue,
            'tipValue' => $this->tipValue,
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(
            TabId::fromString($payload['tabId']),
            $payload['amountPaid'],
            $payload['orderValue'],
            $payload['tipValue'],
        );
    }
}