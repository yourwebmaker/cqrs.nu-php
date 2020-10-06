<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\OrderedItem;

final class FoodServed
{
    public string $tabId;
    /** @var array<int> */
    public array $menuNumbers;
}