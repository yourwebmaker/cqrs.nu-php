<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use Doctrine\ORM\Mapping as ORM;

class Tab
{
    public int $tableNumber;
    public string $waiter;
    /** @var array<TabItem> */
    public array $toServe;
    /** @var array<TabItem> */
    public array $inPreparation;
    /** @var array<TabItem> */
    public array $served;

    public function __construct(int $ableNumber, string $waiter, array $toServe, array $inPreparation, array $served)
    {
        $this->tableNumber = $ableNumber;
        $this->waiter = $waiter;
        $this->toServe = $toServe;
        $this->inPreparation = $inPreparation;
        $this->served = $served;
    }
}