<?php

declare(strict_types=1);

namespace Cafe\Aggregate;

use Cafe\Aggregate\Events\DrinksOrdered;
use Cafe\Aggregate\Events\FoodOrdered;
use Cafe\Aggregate\Events\TabOpened;
use PHPUnit\Framework\TestCase;

class TabTests extends TestCase
{
    private string $tabId;
    private int $testTable;
    private string $testWaiter;
    private OrderedItem $drink1, $drink2, $food1;

    public function setUp(): void
    {
        $this->tabId = 'tab-123';
        $this->testTable = 42;
        $this->testWaiter = 'Derek';

        $this->drink1 = new OrderedItem('d-1', 'Vodka', true, 5.00);
        $this->drink2 = new OrderedItem('d-2', 'Beer', true, 3.00);

        $this->food1 = new OrderedItem('f-2', 'Pasta', false, 10.00);
    }

    public function testOpenTab() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);

        self::assertEquals(
            [
                new TabOpened($this->tabId, $this->testTable, $this->testWaiter)
            ],
            $tab->getRecordedEvents()
        );
    }

    public function testCanPlaceDrinksOrder() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1, $this->drink2]);

        self::assertEquals(
            [
                new TabOpened($this->tabId, $this->testTable, $this->testWaiter),
                new DrinksOrdered($this->tabId, [$this->drink1, $this->drink2])
            ],
            $tab->getRecordedEvents()
        );
    }

    public function testCanPlaceFoodOrder() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->food1]);

        self::assertEquals(
            [
                new TabOpened($this->tabId, $this->testTable, $this->testWaiter),
                new FoodOrdered($this->tabId, [$this->food1]),
            ],
            $tab->getRecordedEvents()
        );
    }

    public function testCanPlaceFoodAndDrinkOrder() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->food1, $this->drink2]);

        self::assertEquals(
            [
                new TabOpened($this->tabId, $this->testTable, $this->testWaiter),
                new DrinksOrdered($this->tabId, [$this->drink2]),
                new FoodOrdered($this->tabId, [$this->food1]),
            ],
            $tab->getRecordedEvents()
        );
    }

    public function testOrderedDrinksCanBeServed() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1, $this->drink2]);
        $tab->markDrinksServed(['d-1', 'd2']);

        //assert DrinksServed("d-1"), DrinksServed("d-2") are recorded
    }

    /**
     * You can not mark a drink as served if it wasn't ordered in the first place
     */
    public function testCanNotServeAnUnorderedDrink() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1]);
        $tab->markDrinksServed(['d2']);

        //assert DrinksNotOutstanding is thrown
    }

    /**
     * You can not mark a drink as served if you already served it
     */
    public function testCanNotServeAnOrderedDrinkTwice() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1]);
        $tab->markDrinksServed(['d1']);
        $tab->markDrinksServed(['d1']);
    }
}