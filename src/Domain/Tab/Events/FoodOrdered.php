<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\OrderedItem;
use Cafe\Domain\Tab\TabId;
use EventSauce\EventSourcing\Serialization\SerializablePayload;

//todo this class has the same code as DrinksOrdered. Maybe be create an abstract one?
final class FoodOrdered implements SerializablePayload
{
    public TabId $tabId;
    /** @var array<OrderedItem> */
    public array $items;

    public function __construct(TabId  $tabId, array $items)
    {
        $this->tabId = $tabId;
        $this->items = $items;
    }

    public function toPayload(): array
    {
        return [
            'tabId' => $this->tabId->toString(),
            'items' => array_map(fn(OrderedItem $item) => $item->jsonSerialize(), $this->items)
        ];
    }

    public static function fromPayload(array $payload): SerializablePayload
    {
        return new self(
            TabId::fromString($payload['tabId']),
            array_map(fn(array $item) => new OrderedItem(
                $item['menuNumber'],
                $item['description'],
                $item['isDrink'],
                $item['price']
            ), $payload['items'])
        );
    }
}