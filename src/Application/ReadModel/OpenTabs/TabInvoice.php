<?php

declare(strict_types=1);

namespace Cafe\Application\ReadModel\OpenTabs;

final class TabInvoice
{
    public string $tabId;

    public int $tableNumber;
    /** @var array<TableTodoItem> */
    public array $items;

    public float $total;

    public bool $hasUnservedItems;

    public function __construct(string $tabId, int $tableNumber, array $items, float $total, bool $hasUnservedItems)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->items = $items;
        $this->total = $total;
        $this->hasUnservedItems = $hasUnservedItems;
    }
}