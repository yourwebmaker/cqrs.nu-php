<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class MarkFoodPreparedCommand
{
    public string $tabId;
    public string $groupId;
    /** @var array<int>  */
    public array $menuNumbers;

    public function __construct(string $tabId, string $groupId, array $menuNumbers)
    {
        $this->tabId = $tabId;
        $this->groupId = $groupId;
        $this->menuNumbers = $menuNumbers;
    }
}