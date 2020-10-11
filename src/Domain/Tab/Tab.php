<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

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
        $this->outstandingFood = $event->items;
    }

    public function applyTabClosed(TabClosed $event) : void
    {
        $this->open = false;
    }

    /**
     * @param string[] $menuNumbers
     */
    public function markDrinksServed(array $menuNumbers) : void
    {
        foreach ($menuNumbers as $menuNumber) {
            if (!isset($this->outstandingDrinks[$menuNumber])) {
                throw new DrinksNotOutstanding("Trying to serve drink '$menuNumber' but it was not ordered yet");
            }
        }

        foreach ($menuNumbers as $menuNumber) {
            unset($this->outstandingDrinks[$menuNumber]);
        }

        $event = new DrinksServed($this->tabId, $menuNumbers);
        $this->recordEvent($event);
    }

    public function close(float $amountPaid) : void
    {
        $tip = $amountPaid - $this->itemsServedValue;

        if ($amountPaid < $this->itemsServedValue) {
            throw NotPaidInFull::withTotals($amountPaid, $this->itemsServedValue);
        }

        $notServed = count($this->outstandingDrinks);
        if ($notServed > 0) {
            throw ItemsNotServed::withTotals($notServed);
        }

        $this->recordEvent(new TabClosed($this->tabId, $amountPaid, $this->itemsServedValue, $tip));
    }
}