<?php

declare(strict_types=1);

namespace Cafe\Aggregate;

use Cafe\Aggregate\Events\TabClosed;
use Cafe\Aggregate\Exception\DrinksNotOutstanding;
use Cafe\Aggregate\Events\DrinksOrdered;
use Cafe\Aggregate\Events\DrinksServed;
use Cafe\Aggregate\Events\FoodOrdered;
use Cafe\Aggregate\Events\TabOpened;
use Cafe\Aggregate\Exception\TabNotPaidInFull;
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
        $menuNumbers = [$this->drink1->menuNumber, $this->drink2->menuNumber];
        $tab->markDrinksServed($menuNumbers);

        self::assertEquals(
            [
                new TabOpened($this->tabId, $this->testTable, $this->testWaiter),
                new DrinksOrdered($this->tabId, [$this->drink1, $this->drink2]),
                new DrinksServed($this->tabId, $menuNumbers),
            ],
            $tab->getRecordedEvents()
        );
    }

    public function testCanNotServeAnUnorderedDrink() : void
    {
        $this->expectException(DrinksNotOutstanding::class);

        //todo this message must be produced
        //$this->expectExceptionMessage("Trying to serve drink '{$this->drink2->menuNumber}' but it was not ordered yet");

        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1]);
        $tab->markDrinksServed([$this->drink2->menuNumber]);
    }

    public function testCanNotServeAnOrderedDrinkTwice() : void
    {
        $this->expectException(DrinksNotOutstanding::class);

        //todo this message must be produced
        //$this->expectExceptionMessage("Trying to serve drink '{$this->drink1->menuNumber}' but it was already served");

        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1]);
        $tab->markDrinksServed([$this->drink1->menuNumber]);
        $tab->markDrinksServed([$this->drink1->menuNumber]);
    }

    public function testCanCloseTabWithTip() : void
    {
        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink2]);
        $menuNumbers = [$this->drink2->menuNumber];
        $tab->markDrinksServed($menuNumbers);
        $tab->close($this->drink2->price + 0.5);

        self::assertEquals(
            [
                new TabOpened($this->tabId, $this->testTable, $this->testWaiter),
                new DrinksOrdered($this->tabId, [$this->drink2]),
                new DrinksServed($this->tabId, $menuNumbers),
                new TabClosed($this->tabId, $this->drink2->price + 0.5, $this->drink2->price, 0.5)
            ],
            $tab->getRecordedEvents()
        );
    }

    public function testCannotCloseTabWithoutPayingInFull() : void
    {
        $this->expectException(TabNotPaidInFull::class);

        $tab = Tab::open($this->tabId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink2]);
        $menuNumbers = [$this->drink2->menuNumber];
        $tab->markDrinksServed($menuNumbers);
        $tab->close(2.00);
    }


    //Upon closing a tab, it must be paid for in full.
    //A tab with unserved items cannot be closed unless the items are either marked as served or cancelled first.
}