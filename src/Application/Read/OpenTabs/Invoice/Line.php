<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs\Invoice;

class Line
{
    public string $description;
    public int $quantity;
    public float $priceEach;
    public float $subTotal;

    public function __construct(string $description, int $quantity, float $priceEach, float $subTotal)
    {
        $this->description = $description;
        $this->quantity = $quantity;
        $this->priceEach = $priceEach;
        $this->subTotal = $subTotal;
    }
}