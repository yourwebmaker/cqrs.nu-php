<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class CloseTabCommand
{
    public function __construct(public string $tabId, public float $amountPaid)
    {
    }
}
