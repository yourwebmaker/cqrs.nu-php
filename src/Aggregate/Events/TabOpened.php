<?php

declare(strict_types=1);

namespace Cafe\Aggregate\Events;

final class TabOpened
{
    public string $tabId;
    public int $tableNumber;
    public string $waiter;

    public function __construct($tabId, $tableNumber, $waiter)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
    }
}