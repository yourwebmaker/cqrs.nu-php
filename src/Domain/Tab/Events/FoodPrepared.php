<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class FoodPrepared implements SerializablePayload
{
    public string $tabId;
    public string $groupId;
    /** @var int[] */
    public array $menuNumbers;

    public function __construct(string $tabId, string $groupId, array $menuNumbers)
    {
        $this->tabId       = $tabId;
        $this->groupId     = $groupId;
        $this->menuNumbers = $menuNumbers;
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId,
            'groupId' => $this->groupId,
            'menuNumbers' => $this->menuNumbers,
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self($payload['tabId'], $payload['groupId'], $payload['menuNumbers']);
    }
}
