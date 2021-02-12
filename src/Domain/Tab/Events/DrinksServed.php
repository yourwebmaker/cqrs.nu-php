<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class DrinksServed implements SerializablePayload
{
    public function __construct(
        public string $tabId,
        /** @var int[] */
        public array $menuNumbers
    ) {}

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId,
            'menuNumbers' => $this->menuNumbers
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self($payload['tabId'], $payload['menuNumbers']);
    }
}