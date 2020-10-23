<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\TabId;

class MarkFoodPrepared
{
    public TabId $tabId;
    public string $groupId;
    /** @var array<int>  */
    public array $menuNumbers;

    public function __construct(string $tabId, string $groupId, array $menuNumbers)
    {
        $this->tabId = TabId::fromString($tabId);
        $this->groupId = $groupId;
        $this->menuNumbers = $menuNumbers;
    }
}