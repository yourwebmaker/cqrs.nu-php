<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

class MarkFoodPreparedCommand
{
    public function __construct(public string $tabId, public string $groupId, public array $menuNumbers)
    {
    }
}
