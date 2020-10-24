<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\TabId;

class MarkFoodServedCommand
{
    public TabId $tabId;
    public array $menuNumbers;

    public function __construct(string $tabId, array $menuNumbers)
    {
        $this->tabId = TabId::fromString($tabId);
        $this->menuNumbers = $menuNumbers;
    }
}