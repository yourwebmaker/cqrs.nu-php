<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Model;

//todo add doc why I need this class. It's for decoupling (before I forget ahahah)
class OrderItem
{
    public int $menuNumber;
    public string $description;
    public int $numberToOrder;

    public function __construct(int $menuNumber, string $description, int $numberToOrder)
    {
        $this->menuNumber = $menuNumber;
        $this->description = $description;
        $this->numberToOrder = $numberToOrder;
    }
}