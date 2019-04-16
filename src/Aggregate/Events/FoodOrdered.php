<?php

declare(strict_types=1);

namespace Cafe\Aggregate\Events;

use Cafe\Aggregate\OrderedItem;

final class FoodOrdered
{
    public string $tabId;

    /** @var OrderedItem[] */
    public $items;

    public function __construct($tabId, array $items)
    {
        $this->tabId = $tabId;
        $this->items = $items;
    }
}