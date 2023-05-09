<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class PlaceOrderCommand
{
    public function __construct(public string $tabId, public array $items)
    {
    }
}
