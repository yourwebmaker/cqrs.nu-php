<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

final class DrinksServed
{
    public string $tabId;
    /** @var int[] */
    public array $menuNumbers;

    public function __construct($tabId, array $menuNumbers)
    {
        $this->tabId = $tabId;
        $this->menuNumbers = $menuNumbers;
    }
}