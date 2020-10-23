<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\TabId;

class CloseTabCommand
{
    public TabId $tabId;
    public float $amountPaid;

    public function __construct(TabId $tabId, float $amountPaid)
    {
        $this->tabId = $tabId;
        $this->amountPaid = $amountPaid;
    }
}