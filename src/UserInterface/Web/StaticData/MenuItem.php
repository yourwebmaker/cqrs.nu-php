<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\StaticData;

final class MenuItem
{
    public function __construct(
        public int $menuNumber,
        public string $description,
        public float $price,
        public bool $isDrink = false,
    ) {
    }
}
