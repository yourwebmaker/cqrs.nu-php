<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\TabId;

final class OpenTabCommand
{
    public TabId $tabId;
    public int $tableNumber;
    public string $waiter;

    public function __construct(string $tabId, int $tableNumber, string $waiter)
    {
        $this->tabId = TabId::fromString($tabId);
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
    }
}