<?php

declare(strict_types=1);

namespace Cafe\Application\Read\ChefTodoList;

final class TodoListItem
{
    public int $menuNumber;
    public string $description;

    public function __construct(int $menuNumber, string $description)
    {
        $this->menuNumber  = $menuNumber;
        $this->description = $description;
    }
}
