<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

final class OpenTabCommand
{
    public function __construct(public string $tabId, public int $tableNumber, public string $waiter)
    {
    }
}
