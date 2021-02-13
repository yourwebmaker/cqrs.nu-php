<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use function count;

final class TabStatus
{
    public string $tabId;
    public int $tableNumber;
    /** @var array<TabItem> */
    public array $toServe;
    /** @var array<TabItem> */
    public array $inPreparation;
    /** @var array<TabItem> */
    public array $served;

    public function __construct(string $tabId, int $tableNumber, array $toServe, array $inPreparation, array $served)
    {
        $this->tabId         = $tabId;
        $this->tableNumber   = $tableNumber;
        $this->toServe       = $toServe;
        $this->inPreparation = $inPreparation;
        $this->served        = $served;
    }

    /**
     * @return array<TabItem>
     */
    public function getItemsToServe(): array
    {
        return $this->toServe;
    }

    public function hasItemsToServe(): bool
    {
        return count($this->toServe) > 0;
    }

    public function getItemsInPreparation(): array
    {
        return $this->inPreparation;
    }

    public function hasItemsInPreparation(): bool
    {
        return count($this->inPreparation) > 0;
    }

    public function getServedItems(): array
    {
        return $this->served;
    }

    public function hasItemsServed(): bool
    {
        return count($this->served) > 0;
    }
}
