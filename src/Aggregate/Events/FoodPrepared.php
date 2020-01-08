<?php

declare(strict_types=1);

namespace Cafe\Aggregate\Events;

final class FoodPrepared
{
    public string $tabId;
    /** @var array<int> */
    public array $menuNumbers;
}