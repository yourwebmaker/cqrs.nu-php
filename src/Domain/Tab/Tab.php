<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use Cafe\Application\Write\CloseTabCommand;
use Cafe\Application\Write\MarkFoodPreparedCommand;
use Cafe\Application\Write\MarkFoodServedCommand;
use Cafe\Application\Write\PlaceOrderCommand;
use Cafe\Domain\Tab\Events\FoodPrepared;
use Cafe\Domain\Tab\Events\FoodServed;
use Cafe\Domain\Tab\Events\TabClosed;
use Cafe\Domain\Tab\Events\TabOpened;
use Cafe\Domain\Tab\Events\DrinksOrdered;
use Cafe\Domain\Tab\Events\DrinksServed;
use Cafe\Domain\Tab\Events\FoodOrdered;
use Cafe\Domain\Tab\Exception\DrinksNotOutstanding;
use Cafe\Domain\Tab\Exception\FoodNotOutstanding;
use Cafe\Domain\Tab\Exception\FoodNotPrepared;
use Cafe\Domain\Tab\Exception\MustPayEnough;
use Cafe\Domain\Tab\Exception\TabHasUnservedItems;
use Cafe\Domain\Tab\Exception\TabNotOpen;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviourWithRequiredHistory;
use EventSauce\EventSourcing\AggregateRootId;
use EventSauce\EventSourcing\UuidAggregateRootId;

final class Tab implements AggregateRoot
{
    use AggregateRootBehaviourWithRequiredHistory;

    private string $tabId;
    private bool $open = false;
    private Collection $outstandingDrinks;
    private Collection $outstandingFood;
    private Collection $preparedFood;
    private float $servedItemsValue = 0.0;

    private function __construct(AggregateRootId $aggregateRootId)
    {
        $this->aggregateRootId = $aggregateRootId;
        $this->outstandingDrinks = new ArrayCollection();
        $this->outstandingFood = new ArrayCollection();
        $this->preparedFood = new ArrayCollection();
    }

    public static function open(string $tabId, int $tableNumber, string $waiter) : self
    {
        $tab = new static(UuidAggregateRootId::fromString($tabId));

        $tab->recordThat(new TabOpened($tabId, $tableNumber, $waiter));

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

    public function markDrinksServed(array $menuNumbers) : void
    {
        if (!$this->areDrinksOutstanding($menuNumbers)) {
            throw new DrinksNotOutstanding();
        }

        $this->recordThat(new DrinksServed($this->tabId, $menuNumbers));
    }

    public function markFoodPrepared(MarkFoodPreparedCommand $command) : void
    {
        if (!$this->isFoodOutstanding($command->menuNumbers)) {
            throw new FoodNotOutstanding();
        }

        $this->recordThat(new FoodPrepared($command->tabId, $command->groupId, $command->menuNumbers));
    }

    public function markFoodServed(MarkFoodServedCommand $command): void
    {
        if (!$this->isFoodPrepared($command->menuNumbers)) {
            throw new FoodNotPrepared();
        }

        $this->recordThat(new FoodServed($command->tabId, $command->menuNumbers));
    }

    public function close(CloseTabCommand $command): void
    {
        if (!$this->open) {
            throw new TabNotOpen();
        }

        if ($this->hasUnservedItems()) {
            throw new TabHasUnservedItems();
        }

        if ($command->amountPaid < $this->servedItemsValue) {
            throw new MustPayEnough();
        }

        $this->recordThat(new TabClosed(
            $command->tabId,
            $command->amountPaid,
            $this->servedItemsValue,
            $command->amountPaid - $this->servedItemsValue
        ));
    }

    private function applyTabOpened(TabOpened $event): void
    {
        $this->open = true;
        $this->tabId = $event->tabId;
    }

    private function applyDrinksOrdered(DrinksOrdered $event): void
    {
        $this->outstandingDrinks = new ArrayCollection();
        foreach ($event->items as $item) {
            $this->outstandingDrinks->add($item);
        }
    }

    private function applyFoodOrdered(FoodOrdered $event) : void
    {
        $this->outstandingFood = new ArrayCollection();
        foreach ($event->items as $item) {
            $this->outstandingFood->add($item);
        }
    }

    private function applyDrinksServed(DrinksServed $event) : void
    {
        foreach ($event->menuNumbers as $num) {
            /** @var OrderedItem $item */
            $item = $this->outstandingDrinks->filter(fn(OrderedItem $drink) => $drink->menuNumber === $num)->first();
            $this->outstandingDrinks->removeElement($item);
            $this->servedItemsValue += $item->price;
        }
    }

    private function applyFoodPrepared(FoodPrepared $event) : void
    {
        foreach ($event->menuNumbers as $num) {
            /** @var OrderedItem $item */
            $item = $this->outstandingFood->filter(fn(OrderedItem $food) => $food->menuNumber === $num)->first();
            $this->outstandingFood->removeElement($item);
            $this->preparedFood->add($item);
        }
    }

    private function applyFoodServed(FoodServed $event) : void
    {
        foreach ($event->menuNumbers as $num) {
            /** @var OrderedItem $item */
            $item = $this->preparedFood->filter(fn(OrderedItem $food) => $food->menuNumber === $num)->first();
            $this->preparedFood->removeElement($item);
            $this->servedItemsValue += $item->price;
        }
    }

    private function applyTabClosed(TabClosed $event) : void
    {
        $this->open = false;
    }

    /**
     * @param array<int> $menuNumbers
     */
    private function areDrinksOutstanding(array $menuNumbers) : bool
    {
        return $this->areAllInList($menuNumbers, $this->outstandingDrinks->toArray());
    }

    /**
     * @param array<int> $menuNumbers
     */
    private function isFoodOutstanding(array $menuNumbers) : bool
    {
        return $this->areAllInList($menuNumbers, $this->outstandingFood->toArray());
    }

    /**
     * @param array<int> $menuNumbers
     */
    private function isFoodPrepared(array $menuNumbers) : bool
    {
        return $this->areAllInList($menuNumbers, $this->preparedFood->toArray());
    }

    /**
     * @param array<int> $want
     * @param array<OrderedItem> $have
     */
    private function areAllInList(array $want, array $have): bool
    {
        //todo... jesus... move this to collection.
        $curHave = array_map(fn(OrderedItem $orderedItem) => $orderedItem->menuNumber, $have);
        foreach ($want as $num) {
            if (($key = array_search($num, $curHave, true)) !== false) {
                unset($curHave[$key]);
            } else {
                return false;
            }
        }

        return true;
    }

    private function hasUnservedItems() : bool
    {
        return $this->outstandingDrinks->count() || $this->outstandingFood->count() || $this->preparedFood->count();
    }
}