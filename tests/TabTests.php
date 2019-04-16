<?php

declare(strict_types=1);

namespace Cafe\Aggregate;

use Cafe\Aggregate\Events\TabOpened;
use PHPUnit\Framework\TestCase;

class TabTests extends TestCase
{
    private string $testId;
    private int $testTable;
    private string $testWaiter;
    //private OrderedItem $drink1, $drink2, $food1;

    public function setUp(): void
    {
        $this->testId = 'tab-123';
        $this->testTable = 42;
        $this->testWaiter = 'Derek';

/*        $this->drink1 = new OrderedItem();
        $this->drink1->description = 'Vodka';
        $this->drink1->isDrink = true;
        $this->drink1->menuNumber = 'd-1';
        $this->drink1->price = 5.00;

        $this->drink2 = new OrderedItem();
        $this->drink2->description = 'Beer';
        $this->drink2->isDrink = true;
        $this->drink2->menuNumber = 'd-2';
        $this->drink2->price = 3.00;

        $this->food1 = new OrderedItem();
        $this->food1->description = 'Pasta';
        $this->food1->isDrink = false;
        $this->food1->menuNumber = 'f-2';
        $this->food1->price = 10.00;*/
    }

    public function testOpenTab() : void
    {
        $tab = Tab::open($this->testId, $this->testTable, $this->testWaiter);
        self::assertEquals(
            [
                new TabOpened($this->testId, $this->testTable, $this->testWaiter)
            ],
            $tab->getRecordedEvents()
        );
    }

    public function testCanPlaceDrinksOrder() : void
    {
        $tab = Tab::open($this->testId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1, $this->drink2]);
        //assert drink ordered
    }

    public function testCanPlaceFoodOrder() : void
    {
        $tab = Tab::open($this->testId, $this->testTable, $this->testWaiter);
        $tab->order([$this->food1]);
        //assert food ordered
    }

    public function testCanPlaceFoodAndDrinkOrder() : void
    {
        $tab = Tab::open($this->testId, $this->testTable, $this->testWaiter);
        $tab->order([$this->food1]);

        // assert food ordered
        // assert drink ordered
    }

    public function testOrderedDrinksCanBeServed() : void
    {
        $tab = Tab::open($this->testId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1, $this->drink2]);
        $tab->markDrinksServed(['d-1', 'd2']);

        //assert DrinksServed("d-1"), DrinksServed("d-2") are recorded
    }

    /**
     * You can not mark a drink as served if it wasn't ordered in the first place
     */
    public function testCanNotServeAnUnorderedDrink() : void
    {
        $tab = Tab::open($this->testId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1]);
        $tab->markDrinksServed(['d2']);

        //assert DrinksNotOutstanding is thrown
    }

    /**
     * You can not mark a drink as served if you already served it
     */
    public function testCanNotServeAnOrderedDrinkTwice() : void
    {
        $tab = Tab::open($this->testId, $this->testTable, $this->testWaiter);
        $tab->order([$this->drink1]);
        $tab->markDrinksServed(['d1']);
        $tab->markDrinksServed(['d1']);
    }
}