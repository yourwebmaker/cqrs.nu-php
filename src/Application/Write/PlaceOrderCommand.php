<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class PlaceOrderCommand
{
    public string $tabId;
    /** @var array<int, int> */
    public array $items;

    public function __construct(string $tabId, array $items)
    {
        $this->tabId = $tabId;
        $this->items = $items;
    }
}