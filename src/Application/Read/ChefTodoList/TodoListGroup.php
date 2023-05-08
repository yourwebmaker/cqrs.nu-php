<?php

declare(strict_types=1);

namespace Cafe\Application\Read\ChefTodoList;

final class TodoListGroup
{
    public function __construct(public string $groupId, public string $tabId, public array $items)
    {
    }
}
