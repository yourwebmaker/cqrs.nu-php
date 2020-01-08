<?php

declare(strict_types=1);

namespace Cafe\ReadModel\ChefTodoList;

final class TodoListGroup
{
    public string $tabId;
    /** @var array<TodoListItem> */
    public array $items;

    /**
     * @param array<TodoListItem> $items
     */
    public function __construct(string $tabId, array $items)
    {
        $this->tabId = $tabId;
        foreach ($items as $item) {
            $this->items[$item->menuNumber] = $item;
        }
    }

    public function removeItem(int $menuNumber) : void
    {
        unset($this->items[$menuNumber]);
    }

    public function removeItems(array $menuNumbers) : void
    {
        foreach ($menuNumbers as $menuNumber) {
            $this->removeItem($menuNumber);
        }
    }
}