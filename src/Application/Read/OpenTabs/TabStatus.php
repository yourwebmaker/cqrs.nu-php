<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use Cafe\UserInterface\Web\StaticData\StaticData;

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

//    public function __construct(string $tabId, int $tableNumber, array $toServe, array $inPreparation, array $served)
//    {
//        $this->tabId = $tabId;
//        $this->tableNumber = $tableNumber;
//        $this->toServe = $toServe;
//        $this->inPreparation = $inPreparation;
//        $this->served = $served;
//    }

    public function getItemsToServe() : array
    {
        return StaticData::getMenu();
    }

    public function hasItemsToServe() : bool
    {
        return true;
    }

    public function hasItemsInPreparation() : bool
    {
        return true;
    }

    public function getItemsInPreparation() : array
    {
        return StaticData::getMenu();
    }

    public function hasItemsServed() : bool
    {
        return true;
    }

    public function getServedItems() : array
    {
        return StaticData::getMenu();
    }
}