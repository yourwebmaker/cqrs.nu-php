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

    public function invoiceForTable(int $table): TabInvoice
    {
        // TODO: Implement invoiceForTable() method.
    }

    public function tabIdForTable(int $tableNumber): string
    {
        $sql = 'select tab_id from read_model_tab where table_number = :table_number';
        return $this->connection->fetchOne($sql, ['table_number' => $tableNumber]);
    }

    public function tabForTable(int $tableNumber): TabStatus
    {
        $tabId = $this->tabIdForTable($tableNumber);
        $rows = $this->connection->fetchAllAssociative('select * from read_model_tab_item where tab_id = :tab_id', [
            'tab_id' => $tabId,
        ]);

        $toServe = [];
        $inPreparation = [];
        $served = [];

        foreach ($rows as $row) {
            $item = new TabItem((int) $row['menu_number'], $row['description'], (float) $row['price']);

            if ($row['status'] === 'to-serve') {
                $toServe[] = $item;
            }

            if ($row['status'] === 'in-preparation') {
                $inPreparation[] = $item;
            }

            if ($row['status'] === 'served') {
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
}