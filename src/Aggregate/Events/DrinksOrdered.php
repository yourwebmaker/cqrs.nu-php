<?php

declare(strict_types=1);

namespace Cafe\Aggregate\Events;

use Cafe\Aggregate\OrderedItem;

final class DrinksOrdered
{
    public string $tabId;

    /** @var OrderedItem[] */
    public $items;
}