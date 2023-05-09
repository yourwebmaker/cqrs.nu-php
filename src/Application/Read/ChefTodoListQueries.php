<?php

declare(strict_types=1);

namespace Cafe\Application\Read;

use Cafe\Application\Read\ChefTodoList\TodoListGroup;

interface ChefTodoListQueries
{
    /** @return array<TodoListGroup> */
    public function getTodoList(): array;
}
