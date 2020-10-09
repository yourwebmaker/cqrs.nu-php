<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

class Tab
{
    public int $ableNumber;
    public string $waiter;
    /** @var array<TabItem> */
    public array $toServe;
    /** @var array<TabItem> */
    public array $inPreparation;
    /** @var array<TabItem> */
    public array $served;
}