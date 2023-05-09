<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class MarkDrinksServedCommand
{
    public function __construct(public string $tabId, public array $menuNumbers)
    {
    }
}
