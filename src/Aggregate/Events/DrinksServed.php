<?php

declare(strict_types=1);

namespace Cafe\Aggregate\Events;

final class DrinksServed
{
    public string $tabId;
    /** @var int[] */
    public array $menuNumbers;
}