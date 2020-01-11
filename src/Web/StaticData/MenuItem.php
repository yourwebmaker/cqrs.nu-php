<?php

declare(strict_types=1);

namespace Cafe\Web\StaticData;

final class MenuItem
{
    public int $menuNumber;
    public string $description;
    public float $price;
    public bool $isDrink;

    public function __construct(int $menuNumber, string $description, float $price, bool $isDrink = false)
    {
        $this->menuNumber = $menuNumber;
        $this->description = $description;
        $this->price = $price;
        $this->isDrink = $isDrink;
    }
}