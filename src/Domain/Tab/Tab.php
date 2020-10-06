<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use Cafe\Domain\Tab\Events\DomainEvent;
use Cafe\Domain\Tab\Events\TabClosed;
use Cafe\Domain\Tab\Events\TabOpened;
use Cafe\Domain\Tab\Events\DrinksOrdered;
use Cafe\Domain\Tab\Events\DrinksServed;
use Cafe\Domain\Tab\Events\FoodOrdered;
use Cafe\Domain\Tab\Exception\DrinksNotOutstanding;
use Cafe\Domain\Tab\Exception\ItemsNotServed;
use Cafe\Domain\Tab\Exception\NotPaidInFull;

final class Tab extends BaseAggregate
{
    public string $tabId;
    private array $outstandingDrinks = [];
    private float $itemsServedValue = 0.0;

    private function __construct()
    {
    }

    public static function open(string $tabId, int $tableNumber, string $waiter) : self
    {
        $tab = new self();
        $tab->tabId = $tabId;

        $tab->recordEvent(new TabOpened($tabId, $tableNumber, $waiter));

        return $tab;
    }

    /**
     * @param DomainEvent[] $events
     * @return Tab
     */
    public static function fromEvents(array $events): Tab
    {
        $tab = new self();
        foreach ($events as $event) {
            $tab->apply($event);
        }

        return $tab;
    }

    /**
     * @param OrderedItem[] $items
     */
    public function order(array $items) : void
    {
        $drinks = array_filter($items, static function (OrderedItem $item) {
            return $item->isDrink;
        });

        if ($drinks) {

            $itemsDrinks = array_values($drinks);

            /** @var OrderedItem $drink */
            foreach ($itemsDrinks as $drink) {
                $this->outstandingDrinks[$drink->menuNumber] = $drink;
                $this->itemsServedValue += $drink->price;
            }

            $this->recordEvent(new DrinksOrdered($this->tabId, $itemsDrinks));
        }

        $food = array_filter($items, static function (OrderedItem $item) {
            return !$item->isDrink;
        });

        if ($food) {
            $this->recordEvent(new FoodOrdered($this->tabId, array_values($food)));
        }
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
            //todo, probably there is a bug here
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

    protected function apply(DomainEvent $event) : void
    {
        switch ($event) {
            case $event instanceof TabOpened:
                $this->tabId = $event->tabId;
                break;
        }
    }
}