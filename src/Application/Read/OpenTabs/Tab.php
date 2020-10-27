<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

class Tab
{
    public string $tabId;
    public int $tableNumber;
    public string $waiter;
    /** @var array<TabItem> */
    public array $toServe;
    /** @var array<TabItem> */
    public array $inPreparation;
    /** @var array<TabItem> */
    public array $served;

    public function __construct(string $tabId, int $tableNumber, string $waiter, array $toServe, array $inPreparation, array $served)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
        $this->toServe = $toServe;
        $this->inPreparation = $inPreparation;
        $this->served = $served;
    }
}