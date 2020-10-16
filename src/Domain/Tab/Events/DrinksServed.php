<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\TabId;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

final class DrinksServed implements SerializablePayload
{
    public TabId $tabId;
    /** @var int[] */
    public array $menuNumbers;

    public function __construct($tabId, array $menuNumbers)
    {
        $this->tabId = $tabId;
        $this->menuNumbers = $menuNumbers;
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId->toString(),
            'menuNumbers' => $this->menuNumbers
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(TabId::fromString($payload['tabId']), $payload['menuNumbers']);
    }
}