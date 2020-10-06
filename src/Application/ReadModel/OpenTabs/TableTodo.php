<?php

declare(strict_types=1);

namespace Cafe\Application\ReadModel\OpenTabs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class TableTodo
{
    public int $tableNumber;

    public string $waiter;
    /** @var array|Collection<TableTodoItem> */
    public $toServe;
    /** @var array|Collection<TableTodoItem> */
    public $inPreparation = [];
    /** @var array|Collection<TableTodoItem> */
    public $served = [];

    public function __construct(int $tableNumber, string $waiter, array $toServe, array $inPreparation, array $served)
    {
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
        $this->toServe = new ArrayCollection($toServe);
        $this->inPreparation = $inPreparation;
        $this->served = new ArrayCollection($served);
    }
}