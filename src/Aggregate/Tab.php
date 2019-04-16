<?php

declare(strict_types=1);

namespace Cafe\Aggregate;

use Cafe\Aggregate\Events\TabOpened;
use Cafe\Aggregate\Events\DrinksOrdered;
use Cafe\Aggregate\Events\DrinksServed;
use Cafe\Aggregate\Events\FoodOrdered;

final class Tab extends BaseAggregate
{
    public string $tabId;
    private array $outstandingDrinks = [];


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
     * @param OrderedItem[] $items
     */
    public function order(array $items) : void
    {
        foreach ($items as $item) {
            if ($item->isDrink) {
                $this->recordEvent(new DrinksOrdered($this->tabId, $item));
            } else {
                //do not trigger one event per item, group them instead
                $this->recordEvent(new FoodOrdered($this->tabId, $item));
            }
        }

    }

    /**
     * @param string[] $menuNumbers
     */
    public function markDrinksServed(array $menuNumbers) : void
    {
        $event = new DrinksServed();
        $event->tabId = $this->tabId;
        $event->menuNumbers = $menuNumbers;

        $this->recordEvent($event);
    }
}