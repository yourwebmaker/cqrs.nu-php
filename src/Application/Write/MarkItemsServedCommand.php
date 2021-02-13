<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class MarkItemsServedCommand
{
    public string $tabId;
    /** @var array<int> */
    public array $menuNumbers;

    public function __construct(string $tabId, array $menuNumbers)
    {
        $this->tabId       = $tabId;
        $this->menuNumbers = $menuNumbers;
    }
}
