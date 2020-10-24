<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\TabId;

class MarkDrinksServedComman
{
    public TabId $tabId;
    /** @var array<int>  */
    public array $menuNumbers;

    public function __construct(string $tabId, array $menuNumbers)
    {
        $this->tabId = TabId::fromString($tabId);
        $this->menuNumbers = $menuNumbers;
    }
}