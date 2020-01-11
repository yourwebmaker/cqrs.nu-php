<?php

declare(strict_types=1);

namespace Cafe\ReadModel\OpenTabs;

use Cafe\Aggregate\Events\DrinksOrdered;
use Cafe\Aggregate\Events\DrinksServed;
use Cafe\Aggregate\Events\FoodOrdered;
use Cafe\Aggregate\Events\FoodPrepared;
use Cafe\Aggregate\Events\FoodServed;
use Cafe\Aggregate\Events\TabClosed;
use Cafe\Aggregate\Events\TabOpened;
use Doctrine\Common\Collections\Collection;
use DomainException;
use RuntimeException;

final class OpenTabs
{

    /** @var array<string, TableTodo> */
    public array $todoByTab = []; //todo make it private

    /**
     * @return array<int>
     */
    public function activeTableNumbers() : array
    {
        return array_values(array_map(static function (TableTodo $tableTodo) {
            return $tableTodo->tableNumber;
        }, $this->todoByTab));
    }

    //public Dictionary<int, List<TabItem>> TodoListForWaiter(string waiter)
    public function todoListForWaiter(string $waiter) : array
    {
        throw new RuntimeException('Not implemented');
        /*
            return (from tab in todoByTab
                    where tab.Value.Waiter == waiter
                    select new
                        {
                                    TableNumber = tab.Value.TableNumber,
                                    ToServe = CopyItems(tab.Value, t => t.ToServe)
                        }
            )
                .Where(t => t.ToServe.Count > 0)
                .ToDictionary(k => k.TableNumber, v => v.ToServe);
        */
    }

    public function tabIdForTable(int $tableNumber) : string
    {
        /** @var TableTodo $item */
        foreach ($this->todoByTab as $tabId => $item) {
            if ($item->tableNumber === $tableNumber) {
                return $tabId;
            }
        }

        throw new DomainException('Could not find any tab for table ' .  $tableNumber);
    }

    public function tabStatusForTable(int $tableNumber) : TabStatus
    {
        /** @var TableTodo $tableTodo */
        foreach ($this->todoByTab as $tabId => $tableTodo) {
            if ($tableTodo->tableNumber === $tableNumber) {
                return new TabStatus(
                    $tabId,
                    $tableNumber,
                    $tableTodo->toServe,
                    $tableTodo->inPreparation,
                    $tableTodo->served,
                );
            }
        }

        throw new DomainException('No table found by number ' .  $tableNumber);
    }

    public function invoiceForTable(int $table) : TabInvoice
    {
        throw new RuntimeException('Not implemented');
        /*KeyValuePair<Guid, Tab> tab;
        lock (todoByTab)
            tab = todoByTab.First(t => t.Value.TableNumber == table);

        lock (tab.Value)
            return new TabInvoice
            {
            TabId = tab.Key,
                TableNumber = tab.Value.TableNumber,
                Items = new List<TabItem>(tab.Value.Served),
                Total = tab.Value.Served.Sum(i => i.Price),
                HasUnservedItems = tab.Value.InPreparation.Any() || tab.Value.ToServe.Any()
            };*/
    }

    public function handleTabOpened(TabOpened $event) : void
    {
        $this->todoByTab[$event->tabId] = new TableTodo($event->tableNumber, $event->waiter, [], [], []);
    }

    public function handleDrinksOrdered(DrinksOrdered $event) : void
    {
        $tableTodoItems = [];
        foreach ($event->items as $orderedItem) {
            $tableTodoItems[] = new TableTodoItem(
                $orderedItem->menuNumber,
                $orderedItem->description,
                $orderedItem->price
            );
        }

        $tab = $this->getTableTodo($event->tabId);
        $this->addItems($event->tabId, $tableTodoItems, $tab->toServe);
    }

    public function handleFoodOrdered(FoodOrdered $event) : void
    {
        $tableTodoItems = [];
        foreach ($event->items as $orderedItem) {
            $tableTodoItems[] = new TableTodoItem(
                $orderedItem->menuNumber,
                $orderedItem->description,
                $orderedItem->price
            );
        }

        $tab = $this->getTableTodo($event->tabId);
        $this->addItems($event->tabId, $tableTodoItems, $tab->inPreparation);
    }

    public function handleFoodPrepared(FoodPrepared $event) : void
    {
        $tableTodo = $this->getTableTodo($event->tabId);
        $this->moveItems($event->tabId, $event->menuNumbers, $tableTodo->inPreparation, $tableTodo->toServe);
    }

    public function handleDrinksServed(DrinksServed $event) : void
    {
        $tab = $this->getTableTodo($event->tabId);
        $this->moveItems(
            $event->tabId,
            $event->menuNumbers,
            $tab->toServe,
            $tab->served
        );
    }

    public function handleFoodServed(FoodServed $event) : void
    {
        $tableTodo = $this->getTableTodo($event->tabId);
        $this->moveItems($event->tabId, $event->menuNumbers, $tableTodo->toServe, $tableTodo->served);
    }

    public function handleTabClosed(TabClosed $event) : void
    {
        unset($this->todoByTab[$event->tabId]);
    }

    private function getTableTodo(string $tabId) : TableTodo
    {
        return $this->todoByTab[$tabId];
    }

    private function addItems(string $tabId, array $newItems, Collection $list) : void
    {
        foreach ($newItems as $newItem) {
            $list->add($newItem);
        }
    }

    /**
     * @todo fix this method
     */
    private function moveItems(string $tabId, array $menuNumbers, Collection $fromList, Collection $toList) : void
    {
        foreach ($menuNumbers as $menuNumber) {
            $foundItem = $fromList->filter(fn(TableTodoItem $item) => $item->menuNumber === $menuNumber)->first();
            $fromList->removeElement($foundItem);
            $toList->add($foundItem);
        }
    }
}