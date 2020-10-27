<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use Cafe\Application\Write\CloseTabCommand;
use Cafe\Application\Write\MarkDrinksServedCommand;
use Cafe\Application\Write\MarkFoodPreparedCommand;
use Cafe\Application\Write\MarkFoodServedCommand;
use Cafe\Application\Write\OpenTabCommand;
use Cafe\Application\Write\PlaceOrderCommand;
use Cafe\Domain\Tab\Events\FoodPrepared;
use Cafe\Domain\Tab\Events\FoodServed;
use Cafe\Domain\Tab\Events\TabClosed;
use Cafe\Domain\Tab\Exception\DrinksNotOutstanding;
use Cafe\Domain\Tab\Events\DrinksOrdered;
use Cafe\Domain\Tab\Events\FoodOrdered;
use Cafe\Domain\Tab\Events\TabOpened;
use Cafe\Domain\Tab\Exception\FoodNotOutstanding;
use Cafe\Domain\Tab\Exception\FoodNotPrepared;
use Cafe\Domain\Tab\Exception\MustPayEnough;
use Cafe\Domain\Tab\Exception\TabHasUnservedItems;
use Cafe\Domain\Tab\Exception\TabNotOpen;
use PHPUnit\Framework\TestCase;

/**
 * Todo Remove duplication in these (e.g [$this->food1, $this->food1]).
 * Todo Do not use commands
 * Todo Assert only 1 event. Create custom assertion
 */
class TabTest extends TestCase
{
    private string $tabId;
    private int $tableNumber;
    private string $waiter;
    private OrderedItem $drink1, $drink2, $food1, $food2;

    public function setUp(): void
    {
        $this->tabId = 'tab-123';
        $this->tableNumber = 42;
        $this->waiter = 'Derek';

        $this->drink1 = new OrderedItem(4, 'Sprite', true, 5.00);
        $this->drink2 = new OrderedItem(10, 'Beer', true, 3.00);
        $this->food1 = new OrderedItem(16, 'Beef Noodles', false, 10.00);
        $this->food2 = new OrderedItem(25, 'Vegetable Curry', false, 10.00);
    }

    public function testOpenTab() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));

        self::assertEquals([
                new TabOpened($this->tabId, $this->tableNumber, $this->waiter)
            ],
            $tab->releaseEvents()
        );
    }

    public function testCanPlaceDrinksOrder() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink1, $this->drink2]));

        self::assertEquals([
                new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
                new DrinksOrdered($this->tabId, [$this->drink1, $this->drink2])
            ],
            $tab->releaseEvents()
        );
    }

    public function testCanPlaceFoodOrder() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food2]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new FoodOrdered($this->tabId, [$this->food1, $this->food2])
        ],
            $tab->releaseEvents()
        );
    }

    public function testCanPlaceFoodAndDrinkOrder() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->drink2]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new DrinksOrdered($this->tabId, [$this->drink2]),
            new FoodOrdered($this->tabId, [$this->food1]),
        ],
            $tab->releaseEvents()
        );
    }

    public function testOrderedDrinksCanBeServed() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->drink2]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new DrinksOrdered($this->tabId, [$this->drink2]),
            new FoodOrdered($this->tabId, [$this->food1]),
        ],
            $tab->releaseEvents()
        );
    }

    public function testCanNotServeAnUnorderedDrink() : void
    {
        $this->expectException(DrinksNotOutstanding::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink1]));
        $tab->markDrinksServed(new MarkDrinksServedCommand($this->tabId, [$this->drink2->menuNumber]));
    }

    public function testCanNotServeAnOrderedDrinkTwice() : void
    {
        $this->expectException(DrinksNotOutstanding::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink1]));
        $tab->markDrinksServed(new MarkDrinksServedCommand($this->tabId, [$this->drink1->menuNumber]));
        $tab->markDrinksServed(new MarkDrinksServedCommand($this->tabId, [$this->drink1->menuNumber]));
    }

    public function testOrderedFoodCanBeMarkedPrepared() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new FoodOrdered($this->tabId, [$this->food1, $this->food1]),
            new FoodPrepared($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]),
        ],
            $tab->releaseEvents()
        );
    }

    public function testFoodNotOrderedCanNotBeMarkedPrepared() : void
    {
        $this->expectException(FoodNotOutstanding::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
    }

    public function testCanNotMarkFoodAsPreparedTwice() : void
    {
        $this->expectException(FoodNotOutstanding::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber]));
    }

    public function testCanServePreparedFood() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
        $tab->markFoodServed(new MarkFoodServedCommand($this->tabId, [$this->food1->menuNumber, $this->food1->menuNumber]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new FoodOrdered($this->tabId, [$this->food1, $this->food1]),
            new FoodPrepared($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]),
            new FoodServed($this->tabId, [$this->food1->menuNumber, $this->food1->menuNumber]),
        ],
            $tab->releaseEvents()
        );
    }

    public function testCanNotServePreparedFoodTwice() : void
    {
        $this->expectException(FoodNotPrepared::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
        $tab->markFoodServed(new MarkFoodServedCommand($this->tabId, [$this->food1->menuNumber, $this->food1->menuNumber]));
        $tab->markFoodServed(new MarkFoodServedCommand($this->tabId, [$this->food1->menuNumber, $this->food1->menuNumber]));
    }

    public function testCanNotServeUnorderedFood() : void
    {
        $this->expectException(FoodNotPrepared::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber]));
        $tab->markFoodServed(new MarkFoodServedCommand($this->tabId, [$this->food2->menuNumber]));
    }

    public function testCanNotServeOrderedButUnpreparedFood() : void
    {
        $this->expectException(FoodNotPrepared::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodServed(new MarkFoodServedCommand($this->tabId, [$this->food2->menuNumber]));
    }

    public function testCanCloseTabByPayingExactAmount() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food2]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food2->menuNumber]));
        $tab->markFoodServed(new MarkFoodServedCommand($this->tabId, [$this->food1->menuNumber, $this->food2->menuNumber]));
        $amountPaid = $this->food1->price + $this->food2->price;
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $amountPaid));

        self::assertContainsEquals(new TabClosed(
            $this->tabId,
            $amountPaid,
            $amountPaid,
            $tip = 0
        ), $tab->releaseEvents()); //todo gambi do carai;
    }

    public function testCanCloseTabWithTip() : void
    {
        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->markDrinksServed(new MarkDrinksServedCommand($this->tabId, [$this->drink2->menuNumber]));
        $amountPaid = $this->drink2->price + 0.50;
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $amountPaid));

        self::assertContainsEquals(new TabClosed(
            $this->tabId,
            $amountPaid,
            $this->drink2->price,
            $tip = 0.50
        ), $tab->releaseEvents()); //todo gambi do carai;
    }

    public function testMustPayEnoughToCloseTab() : void
    {
        $this->expectException(MustPayEnough::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->markDrinksServed(new MarkDrinksServedCommand($this->tabId, [$this->drink2->menuNumber]));
        $amountPaid = $this->drink2->price - 0.50;
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $amountPaid));
    }

    public function testCanNotCloseTabTwice() : void
    {
        $this->expectException(TabNotOpen::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->markDrinksServed(new MarkDrinksServedCommand($this->tabId, [$this->drink2->menuNumber]));
        $amountPaid = $this->drink2->price;
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $amountPaid));
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $amountPaid));
    }

    public function testCanNotCloseTabWithUnservedDrinksItems() : void
    {
        $this->expectException(TabHasUnservedItems::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $this->drink2->price));
    }

    public function testCanNotCloseTabWithUnpreparedFoodItems() : void
    {
        $this->expectException(TabHasUnservedItems::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1]));
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $this->food1->price));
    }

    public function testCanNotCloseTabWithUnservedFoodItems() : void
    {
        $this->expectException(TabHasUnservedItems::class);

        $tab = Tab::open(new OpenTabCommand($this->tabId, $this->tableNumber, $this->waiter));
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber]));
        $tab->close(new CloseTabCommand(TabId::fromString($this->tabId), $this->food1->price));
    }
}