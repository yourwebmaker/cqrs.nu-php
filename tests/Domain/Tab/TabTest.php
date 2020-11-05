<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use Cafe\Application\Write\CloseTabCommand;
use Cafe\Application\Write\MarkFoodPreparedCommand;
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

    /**
     * @test
     */
    public function open_tab() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);

        self::assertEquals([
                new TabOpened($this->tabId, $this->tableNumber, $this->waiter)
            ],
            $tab->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function can_place_drinks_order() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink1, $this->drink2]));

        self::assertEquals([
                new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
                new DrinksOrdered($this->tabId, [$this->drink1, $this->drink2])
            ],
            $tab->releaseEvents()
        );
    }

    /**
    /**
     * @test
     */
    public function can_place_food_order() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food2]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new FoodOrdered($this->tabId, [$this->food1, $this->food2])
        ],
            $tab->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function can_place_food_and_drink_order() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->drink2]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new DrinksOrdered($this->tabId, [$this->drink2]),
            new FoodOrdered($this->tabId, [$this->food1]),
        ],
            $tab->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function ordered_drinks_can_be_served() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->drink2]));

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new DrinksOrdered($this->tabId, [$this->drink2]),
            new FoodOrdered($this->tabId, [$this->food1]),
        ],
            $tab->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function cannot_serve_unordered_drink() : void
    {
        $this->expectException(DrinksNotOutstanding::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink1]));
        $tab->markDrinksServed([$this->drink2->menuNumber]);
    }

    /**
     * @test
     */
    public function cannot_server_an_ordered_drink_twice() : void
    {
        $this->expectException(DrinksNotOutstanding::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink1]));
        $tab->markDrinksServed([$this->drink1->menuNumber]);
        $tab->markDrinksServed([$this->drink1->menuNumber]);
    }

    /**
     * @test
     */
    public function order_food_can_be_marked_prepared() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
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

    /**
     * @test
     */
    public function food_not_ordered_cannot_be_marked_prepared() : void
    {
        $this->expectException(FoodNotOutstanding::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
    }

    /**
     * @test
     */
    public function cannot_marked_food_as_prepared_twice() : void
    {
        $this->expectException(FoodNotOutstanding::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber]));
    }

    /**
     * @test
     */
    public function can_serve_prepared_food() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
        $tab->markFoodServed([$this->food1->menuNumber, $this->food1->menuNumber]);

        self::assertEquals([
            new TabOpened($this->tabId, $this->tableNumber, $this->waiter),
            new FoodOrdered($this->tabId, [$this->food1, $this->food1]),
            new FoodPrepared($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]),
            new FoodServed($this->tabId, [$this->food1->menuNumber, $this->food1->menuNumber]),
        ],
            $tab->releaseEvents()
        );
    }

    /**
     * @test
     */
    public function cannot_serve_prepared_food_twice() : void
    {
        $this->expectException(FoodNotPrepared::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food1->menuNumber]));
        $tab->markFoodServed([$this->food1->menuNumber, $this->food1->menuNumber]);
        $tab->markFoodServed([$this->food1->menuNumber, $this->food1->menuNumber]);
    }

    /**
     * @test
     */
    public function cannot_serve_unordered_food() : void
    {
        $this->expectException(FoodNotPrepared::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber]));
        $tab->markFoodServed([$this->food2->menuNumber]);
    }

    /**
     * @test
     */
    public function cannot_serve_ordered_but_unprepared_food() : void
    {
        $this->expectException(FoodNotPrepared::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food1]));
        $tab->markFoodServed([$this->food2->menuNumber]);
    }

    /**
     * @test
     */
    public function can_close_tab_by_paying_exact_amount() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1, $this->food2]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber, $this->food2->menuNumber]));
        $tab->markFoodServed([$this->food1->menuNumber, $this->food2->menuNumber]);
        $amountPaid = $this->food1->price + $this->food2->price;
        $tab->close(new CloseTabCommand($this->tabId, $amountPaid));

        self::assertContainsEquals(new TabClosed(
            $this->tabId,
            $amountPaid,
            $amountPaid,
            $tip = 0
        ), $tab->releaseEvents()); //todo gambi do carai;
    }

    /**
     * @test
     */
    public function can_close_tab_with_tip() : void
    {
        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->markDrinksServed([$this->drink2->menuNumber]);
        $amountPaid = $this->drink2->price + 0.50;
        $tab->close(new CloseTabCommand($this->tabId, $amountPaid));

        self::assertContainsEquals(new TabClosed(
            $this->tabId,
            $amountPaid,
            $this->drink2->price,
            $tip = 0.50
        ), $tab->releaseEvents()); //todo gambi do carai;
    }

    /**
     * @test
     */
    public function must_pay_enough_to_close_tab() : void
    {
        $this->expectException(MustPayEnough::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->markDrinksServed([$this->drink2->menuNumber]);
        $amountPaid = $this->drink2->price - 0.50;
        $tab->close(new CloseTabCommand($this->tabId, $amountPaid));
    }

    /**
     * @test
     */
    public function cannot_close_tab_twice() : void
    {
        $this->expectException(TabNotOpen::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->markDrinksServed([$this->drink2->menuNumber]);
        $amountPaid = $this->drink2->price;
        $tab->close(new CloseTabCommand($this->tabId, $amountPaid));
        $tab->close(new CloseTabCommand($this->tabId, $amountPaid));
    }

    /**
     * @test
     */
    public function cannot_close_tab_with_unserved_drink_items() : void
    {
        $this->expectException(TabHasUnservedItems::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->drink2]));
        $tab->close(new CloseTabCommand($this->tabId, $this->drink2->price));
    }

    /**
     * @test
     */
    public function cannot_close_tab_with_unprepared_food_items() : void
    {
        $this->expectException(TabHasUnservedItems::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1]));
        $tab->close(new CloseTabCommand($this->tabId, $this->food1->price));
    }

    /**
     * @test
     */
    public function cannot_close_tab_with_unserved_food_items() : void
    {
        $this->expectException(TabHasUnservedItems::class);

        $tab = Tab::open($this->tabId, $this->tableNumber, $this->waiter);
        $tab->order(new PlaceOrderCommand($this->tabId, [$this->food1]));
        $tab->markFoodPrepared(new MarkFoodPreparedCommand($this->tabId, 'groupId', [$this->food1->menuNumber]));
        $tab->close(new CloseTabCommand($this->tabId, $this->food1->price));
    }
}