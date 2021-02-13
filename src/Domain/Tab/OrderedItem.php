<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use JsonSerializable;

final class OrderedItem implements JsonSerializable
{
    public int $menuNumber;
    public string $description;
    public bool $isDrink;
    public float $price;

    public function __construct(int $menuNumber, string $description, bool $isDrink, float $price)
    {
        $this->menuNumber  = $menuNumber;
        $this->description = $description;
        $this->isDrink     = $isDrink;
        $this->price       = $price;
    }

    public function jsonSerialize(): array
    {
        return [
            'menuNumber' => $this->menuNumber,
            'description' => $this->description,
            'isDrink' => $this->isDrink,
            'price' => $this->price,
        ];
    }
}
