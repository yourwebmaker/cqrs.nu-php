<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Events;

final class TabClosed
{
    public string $tabId;
    public float $amountPaid;
    public float $orderValue;
    public float $tipValue;

    public function __construct(string $tabId, float $amountPaid, float $orderValue, float $tipValue)
    {
        $this->tabId = $tabId;
        $this->amountPaid = $amountPaid;
        $this->orderValue = $orderValue;
        $this->tipValue = $tipValue;
    }
}