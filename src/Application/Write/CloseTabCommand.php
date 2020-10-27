<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class CloseTabCommand
{
    public string $tabId;
    public float $amountPaid;

    public function __construct(string $tabId, float $amountPaid)
    {
        $this->tabId = $tabId;
        $this->amountPaid = $amountPaid;
    }
}