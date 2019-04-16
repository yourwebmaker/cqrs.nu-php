<?php

declare(strict_types=1);

namespace Aggregate;

final class OrderedItem
{
    public int $menuNumber;
    public string $description;
    public bool $isDrink;
    public float $price;
}