<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs\Invoice;

class Line
{
    public function __construct(
        public string $description,
        public int $quantity,
        public float $priceEach,
        public float $subTotal,
    ) {
    }
}
