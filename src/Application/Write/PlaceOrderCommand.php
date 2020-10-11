<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\OrderedItem;
use Cafe\Domain\Tab\TabId;

class PlaceOrderCommand
{
    public TabId $tabId;
    /** @var array<OrderedItem> */
    public array $items;

    public function __construct(string $tabId, array $items)
    {
        $this->tabId = TabId::fromString($tabId);
        $this->items = $items;
    }

    public function hasDrinks() : bool
    {
        return count($this->getDrinks()) > 0;
    }

    public function hasFood() : bool
    {
        return count($this->getFood()) > 0;
    }

    /**
     * @return array<OrderedItem>
     */
    public function getDrinks() : array
    {
        return array_filter($this->items, fn (OrderedItem $item) => $item->isDrink);
    }

    public function getFood() : array
    {
        return array_filter($this->items, fn (OrderedItem $item) => !$item->isDrink);
    }
}