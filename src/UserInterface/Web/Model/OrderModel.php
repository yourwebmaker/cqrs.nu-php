<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Model;

class OrderModel
{
    /** @var array<OrderItem>  */
    public array $items;

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function getOnlyOrdered() : array
    {
        return array_filter($this->items, fn(OrderItem $item) => $item->numberToOrder > 0);
    }
}