<?php

declare(strict_types=1);

namespace Cafe\Application\Read\ChefTodoList;

final class TodoListGroup
{
    public string $groupId;
    public string $tabId;
    public array $items;

    public function __construct(string $groupId, string $tabId, array $items)
    {
        $this->groupId = $groupId;
        $this->tabId = $tabId;
        $this->items = $items;
    }
}