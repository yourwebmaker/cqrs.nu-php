<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

final class TableTodoItem
{
    public int $menuNumber;
    public string $description;
    public float $price;

    public function __construct(int $menuNumber, string $description, float $price)
    {
        $this->menuNumber = $menuNumber;
        $this->description = $description;
        $this->price = $price;
    }
}