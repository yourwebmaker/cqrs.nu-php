<?php

declare(strict_types=1);

namespace Cafe\Aggregate;

final class OrderedItem
{
    public int $menuNumber;
    public string $description;
    public bool $isDrink;
    public float $price;

    public function __construct($menuNumber, $description, $isDrink, $price)
    {
        $this->menuNumber = $menuNumber;
        $this->description = $description;
        $this->isDrink = $isDrink;
        $this->price = $price;
    }
}