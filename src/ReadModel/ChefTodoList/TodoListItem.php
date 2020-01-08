<?php

declare(strict_types=1);

namespace Cafe\ReadModel\ChefTodoList;

final class TodoListItem
{
    public int $menuNumber; //1, 2, 3, 4
    public string $description; //Sandwich, Carbonara pasta, Lasagna

    public function __construct(int $menuNumber, string $description)
    {
        $this->menuNumber = $menuNumber;
        $this->description = $description;
    }
}