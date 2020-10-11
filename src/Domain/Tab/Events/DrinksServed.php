<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

use Cafe\Domain\Tab\TabId;

final class DrinksServed
{
    public TabId $tabId;
    /** @var int[] */
    public array $menuNumbers;

    public function __construct($tabId, array $menuNumbers)
    {
        $this->tabId = $tabId;
        $this->menuNumbers = $menuNumbers;
    }
}