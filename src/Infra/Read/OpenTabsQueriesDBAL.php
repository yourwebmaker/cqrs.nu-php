<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Application\Read\OpenTabs\TabInvoice;
use Cafe\Application\Read\OpenTabs\TabItem;
use Cafe\Application\Read\OpenTabs\TabStatus;
use Cafe\Application\Read\OpenTabsQueries;
use Doctrine\DBAL\Connection;

class OpenTabsQueriesDBAL implements OpenTabsQueries
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     */
    public function activeTableNumbers(): array
    {
        $sql = 'select table_number from read_model_tab';
        $tableNumbers = $this->connection->fetchNumeric($sql);

        if (! $tableNumbers) {
            return [];
        }

        return $tableNumbers;
    }

    public function invoiceForTable(int $tableNumber): TabInvoice
    {
        $tabId = $this->tabIdForTable($tableNumber);

        return new TabInvoice($tabId, $tableNumber, $this->hydrateItems($tabId));
    }

    public function tabIdForTable(int $tableNumber): string
    {
        $sql = 'select tab_id from read_model_tab where table_number = :table_number';
        return $this->connection->fetchOne($sql, ['table_number' => $tableNumber]);
    }

    public function tabForTable(int $tableNumber): TabStatus
    {
        $tabId = $this->tabIdForTable($tableNumber);
        $items = $this->hydrateItems($tabId);

        $toServe = [];
        $inPreparation = [];
        $served = [];

        foreach ($items as $item) {
            if ($item->status === TabItem::STATUS_TO_SERVE) {
                $toServe[] = $item;
            }

            if ($item->status === TabItem::STATUS_IN_PREPARATION) {
                $inPreparation[] = $item;
            }

            if ($item->status === TabItem::STATUS_SERVED) {
                $served[] = $item;
            }
        }

        return new TabStatus($tabId, $tableNumber, $toServe, $inPreparation, $served);
    }

    /**
     * @inheritDoc
     */
    public function todoListForWaiter(string $waiter): array
    {
        // TODO: Implement todoListForWaiter() method.
    }

    /**
     * @return array<TabItem>
     */
    private function hydrateItems(string $tabId): array
    {
        $rowsItems = $this->connection->fetchAllAssociative('select * from read_model_tab_item where tab_id = :tab_id', [
            'tab_id' => $tabId,
        ]);

        $items = array_map(fn(array $row) => new TabItem((int)$row['menu_number'], $row['description'], (float)$row['price'], $row['status']), $rowsItems);
        return $items;
    }
}