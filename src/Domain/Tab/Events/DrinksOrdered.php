<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\OrderedItem;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

use function array_map;

final class DrinksOrdered implements SerializablePayload
{
    public function __construct(public string $tabId, public array $items)
    {
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId,
            'items' => array_map(static fn (OrderedItem $item) => $item->jsonSerialize(), $this->items),
        ];
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            $payload['tabId'],
            array_map(static fn (array $item) => new OrderedItem(
                $item['menuNumber'],
                $item['description'],
                $item['isDrink'],
                $item['price'],
            ), $payload['items']),
        );
    }
}
