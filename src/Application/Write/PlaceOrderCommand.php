<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\OrderedItem;

class PlaceOrderCommand
{
    public string $tabId;
    /** @var array<OrderedItem> */
    public array $items;

    public function __construct(string $tabId, array $items)
    {
        $this->tabId = $tabId;
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
        return array_values(array_filter($this->items, fn (OrderedItem $item) => $item->isDrink));
    }

    public function getFood() : array
    {
        return array_values(array_filter($this->items, fn (OrderedItem $item) => !$item->isDrink));
    }
}