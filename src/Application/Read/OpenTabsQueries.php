<?php

declare(strict_types=1);

namespace Cafe\Application\Read;

use Cafe\Application\Read\OpenTabs\TabInvoice;
use Cafe\Application\Read\OpenTabs\TabItem;
use Cafe\Application\Read\OpenTabs\TabStatus;

interface OpenTabsQueries
{
    /**
     * @return array<int>
     */
    public function activeTableNumbers(): array;
    public function invoiceForTable(int $tableNumber): TabInvoice;
    public function tabIdForTable(int $tableNumber) : string;
    public function tabForTable(int $tableNumber) : TabStatus;

    /**
     * @return array<int, array<TabItem>>
     */
    public function todoListForWaiter(string $waiter) : array;
}