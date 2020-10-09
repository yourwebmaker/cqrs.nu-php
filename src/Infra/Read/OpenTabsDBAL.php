<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Application\Read\OpenTabs\TabInvoice;
use Cafe\Application\Read\OpenTabs\TabStatus;
use Cafe\Application\Read\OpenTabsQueries;

class OpenTabsDBAL implements OpenTabsQueries
{
    /**
     * @inheritDoc
     */
    public function activeTableNumbers(): array
    {
        // TODO: Implement activeTableNumbers() method.
    }

    public function invoiceForTable(int $table): TabInvoice
    {
        // TODO: Implement invoiceForTable() method.
    }

    public function tabIdForTable(int $table): string
    {
        // TODO: Implement tabIdForTable() method.
    }

    public function tabForTable(int $table): TabStatus
    {
        // TODO: Implement tabForTable() method.
    }

    /**
     * @inheritDoc
     */
    public function todoListForWaiter(string $waiter): array
    {
        // TODO: Implement todoListForWaiter() method.
    }
}