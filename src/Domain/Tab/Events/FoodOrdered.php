<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\OrderedItem;

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