<?php

declare(strict_types=1);

namespace Cafe\Aggregate\Events;

use Cafe\Aggregate\OrderedItem;

final class FoodServed
{
    public string $tabId;
    /** @var array<int> */
    public array $menuNumbers;
}