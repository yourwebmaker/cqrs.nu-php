<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use Cafe\Application\Write\MarkDrinksServed;
use Cafe\Application\Write\OpenTabCommand;
use Cafe\Application\Write\PlaceOrderCommand;
use Cafe\Domain\Tab\Events\TabClosed;
use Cafe\Domain\Tab\Events\TabOpened;
use Cafe\Domain\Tab\Events\DrinksOrdered;
use Cafe\Domain\Tab\Events\DrinksServed;
use Cafe\Domain\Tab\Events\FoodOrdered;
use Cafe\Domain\Tab\Exception\DrinksNotOutstanding;
use Cafe\Domain\Tab\Exception\ItemsNotServed;
use Cafe\Domain\Tab\Exception\NotPaidInFull;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

final class Tab implements AggregateRoot
{
    use AggregateRootBehaviour;

    private bool $open = false;
    private array $outstandingDrinks;
    private array $outstandingFood;
    private array $preparedFood;
    private float $servedItemsValue;

    public static function open(OpenTabCommand $command) : self
    {
        $tab = new static($command->tabId);

        $tab->recordThat(new TabOpened(
            $command->tabId,
            $command->tableNumber,
            $command->waiter)
        );

        return $tab;
    }

    public function order(PlaceOrderCommand $command) : void
    {
        if ($command->hasDrinks()) {
            $this->recordThat(new DrinksOrdered($command->tabId, $command->getDrinks()));
        }

        if ($command->hasFood()) {
            $this->recordThat(new FoodOrdered($command->tabId, $command->getFood()));
        }
    }

    public function markDrinksServed(MarkDrinksServed $command) : void
    {
        if (!$this->areDrinksOutstanding($command->menuNumbers)) {
            throw new DrinksNotOutstanding();
        }

        $this->recordThat(new DrinksServed($command->tabId, $command->menuNumbers));
    }

    public function applyTabOpened(TabOpened $event): void
    {
        $this->open = true;
    }

    public function applyDrinksOrdered(DrinksOrdered $event): void
    {
        $this->outstandingDrinks[] = $event->items;
    }

    public function applyFoodOrdered(FoodOrdered $event) : void
    {
        $this->outstandingFood[] = $event->items;
    }

    public function applyDrinksServed(DrinksServed $event) : void
    {
        return;
//        foreach ($event->menuNumbers as $menuNumber)
//        {
//            /** @var OrderedItem $item */
//            $item = $this->outstandingDrinks.First(d => d.MenuNumber == num);
//            $this->outstandingDrinksoutstandingDrinks.Remove(item);
//            $this->servedItemsValue += $item->price;
//        }
    }

    public function applyTabClosed(TabClosed $event) : void
    {
        $this->open = false;
    }

    private function areDrinksOutstanding(array $menuNumbers) : bool
    {
        return $this->areAllInList($menuNumbers, $this->outstandingDrinks);
    }

    private function areAllInList(array $menuNumbers, array $list) : bool
    {
        return false;
//        var curHave = new List<int>(have.Select(i => i.MenuNumber));
//            foreach (var num in want)
//                if (curHave.Contains(num))
//                    curHave.Remove(num);
//                else
//                    return false;
//            return true;
    }
}