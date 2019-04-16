<?php

declare(strict_types=1);

namespace Commands;

use Tab;

final class OpenTabHandler
{
    public function handle(OpenTab $command) : void
    {
        Tab::open($command->tabId, $command->tableNumber, $command->waiter);
    }
}