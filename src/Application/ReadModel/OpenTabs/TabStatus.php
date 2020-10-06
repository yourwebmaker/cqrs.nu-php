<?php

declare(strict_types=1);

namespace Cafe\Application\ReadModel\OpenTabs;

final class TabStatus
{
    public string $tabId;

    public int $tableNumber;
    /** @var array<TableTodoItem> */
    public array $toServe;
    /** @var array<TableTodoItem> */
    public array $inPreparation;
    /** @var array<TableTodoItem> */
    public array $served;

    public function __construct(string $tabId, int $tableNumber, array $toServe, array $inPreparation, array $served)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->toServe = $toServe;
        $this->inPreparation = $inPreparation;
        $this->served = $served;
    }
}