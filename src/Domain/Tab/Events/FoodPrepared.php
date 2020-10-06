<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

final class FoodPrepared
{
    public string $tabId;
    /** @var array<int> */
    public array $menuNumbers;
}