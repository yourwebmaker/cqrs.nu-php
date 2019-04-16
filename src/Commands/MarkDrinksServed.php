<?php

declare(strict_types=1);

namespace Commands;

final class MarkDrinksServed
{
    public string $tabId;
    /** @var int[] */
    public array $menuNumbers;
}