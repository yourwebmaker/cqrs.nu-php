<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;
use Cafe\UserInterface\Web\StaticData\MenuItem;
use Cafe\UserInterface\Web\StaticData\StaticData;

class TabHandler
{
    private TabRepository $repository;

    public function __construct(TabRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleOpenTabCommand(OpenTabCommand $command) : void
    {
        $tab = Tab::open($command->tabId, $command->tableNumber, $command->waiter);
        $this->repository->save($tab);
    }

    public function handlePlaceOrderCommand(PlaceOrderCommand $command) : void
    {
        $tab = $this->repository->get($command->tabId);
        $tab->order($command->items);
        $this->repository->save($tab);
    }

    public function handleMarkItemsServedCommand(MarkItemsServedCommand $command) : void
    {
        $tab = $this->repository->get($command->tabId);
        $menu = StaticData::getMenu();

        $drinksNumbers = [];
        $foodNumbers = [];

        /**
         * @var int $menuNumber
         * @var MenuItem $menuItem
         */
        foreach ($command->menuNumbers as $menuNumber) {
            if ($menu[$menuNumber]->isDrink) {
                $drinksNumbers[] = $menuNumber;
            } else {
                $foodNumbers[] = $menuNumber;
            }
        }

        if (count($drinksNumbers) > 0) {
            $tab->markDrinksServed($drinksNumbers);
        }

        if (count($foodNumbers) > 0) {
            $tab->markFoodServed($foodNumbers);
        }

        $this->repository->save($tab);
    }

    public function handleMarkFoodPreparedCommand(MarkFoodPreparedCommand $command) : void
    {
        $tab = $this->repository->get($command->tabId);
        $tab->markFoodPrepared($command->menuNumbers, $command->groupId);
        $this->repository->save($tab);
    }

    public function handleCloseTabCommand(CloseTabCommand $command) : void
    {
        $tab = $this->repository->get($command->tabId);
        $tab->close($command->amountPaid);
        $this->repository->save($tab);
    }
}