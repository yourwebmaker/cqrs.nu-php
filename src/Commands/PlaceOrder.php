<?php

declare(strict_types=1);

namespace Commands;

use Aggregate\OrderedItem;

final class PlaceOrder
{
    public string $tabId;

    /** @var OrderedItem[] */
    public array $items;
}