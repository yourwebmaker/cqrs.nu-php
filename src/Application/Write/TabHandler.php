<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Domain\Tab\OrderedItem;
use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;
use Cafe\UserInterface\Web\StaticData\StaticData;

use function assert;
use function count;
use function is_int;

class TabHandler
{
    private TabRepository $repository;

    public function __construct(TabRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleOpenTabCommand(OpenTabCommand $command): void
    {
        $tab = Tab::open($command->tabId, $command->tableNumber, $command->waiter);
        $this->repository->save($tab);
    }

    public function handlePlaceOrderCommand(PlaceOrderCommand $command): void
    {
        $tab = $this->repository->get($command->tabId);
        //Todo move this to view model
        $menu = StaticData::getMenu();

        $orderedItems = [];
        foreach ($command->items as $itemNumber => $quantity) {
            for ($i = 0; $i < $quantity; $i++) {
                $orderedItems[] = new OrderedItem(
                    $itemNumber,
                    $menu[$itemNumber]->description,
                    $menu[$itemNumber]->isDrink,
                    $menu[$itemNumber]->price,
                );
            }
        }

        $tab->order($orderedItems);
        $this->repository->save($tab);
    }

    public function handleMarkItemsServedCommand(MarkItemsServedCommand $command): void
    {
        $tab  = $this->repository->get($command->tabId);
        $menu = StaticData::getMenu();

        $drinksNumbers = [];
        $foodNumbers   = [];

        foreach ($command->menuNumbers as $menuNumber) {
            assert(is_int($menuNumber));
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

    public function handleMarkFoodPreparedCommand(MarkFoodPreparedCommand $command): void
    {
        $tab = $this->repository->get($command->tabId);
        $tab->markFoodPrepared($command->menuNumbers, $command->groupId);
        $this->repository->save($tab);
    }

    public function handleCloseTabCommand(CloseTabCommand $command): void
    {
        $tab = $this->repository->get($command->tabId);
        $tab->close($command->amountPaid);
        $this->repository->save($tab);
    }
}
