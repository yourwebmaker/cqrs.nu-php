<?php

declare(strict_types=1);

namespace Cafe\Application\Read\ChefTodoList;

final class TodoListItem
{
    public function __construct(public int $menuNumber, public string $description)
    {
    }
}
