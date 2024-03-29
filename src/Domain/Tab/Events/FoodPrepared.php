<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class FoodPrepared implements SerializablePayload
{
    public function __construct(public string $tabId, public string $groupId, public array $menuNumbers)
    {
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId,
            'groupId' => $this->groupId,
            'menuNumbers' => $this->menuNumbers,
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self($payload['tabId'], $payload['groupId'], $payload['menuNumbers']);
    }
}
