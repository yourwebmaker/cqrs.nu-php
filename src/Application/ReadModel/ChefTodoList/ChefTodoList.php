<?php

declare(strict_types=1);

namespace Cafe\Application\ReadModel\ChefTodoList;

use Cafe\Domain\Tab\Events\FoodOrdered;
use Cafe\Domain\Tab\Events\FoodPrepared;

final class ChefTodoList
{
    /** @var TodoListGroup[] */
    private array $todoList = [];

    /**
     * @return TodoListGroup[]
     * @todo implement this method
     */
    public function getTodoList() : array
    {
        return $this->todoList;
        /*lock (todoList)
        return (from grp in todoList
            select new TodoListGroup
            {
                Tab = grp.Tab,
                Items = new List<TodoListItem>(grp.Items)
            }
        ).ToList();*/
    }

    /**
     * @todo test this method
     */
    public function handleFoodOrdered(FoodOrdered $event) : void
    {
        $todoItems = [];

        foreach ($event->items as $item) {
            $todoItems = new TodoListItem($item->menuNumber, $item->description);
        }

        $this->todoList[$event->tabId] = new TodoListGroup($event->tabId, $todoItems);
    }

    /**
     * @todo test this method
     */
    public function handleFoodPrepared(FoodPrepared $event) : void
    {
        /** @var TodoListGroup $group */
        $group = array_filter($this->todoList, function (TodoListGroup $group) use ($event) {
            return $group->tabId === $event->tabId;
        })[0];

        $group->removeItems($event->menuNumbers);

        if (count($group->items) === 0) {
            unset($this->todoList[$group->tabId]);
        }
    }
}