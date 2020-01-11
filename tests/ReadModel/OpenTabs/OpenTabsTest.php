<?php

declare(strict_types=1);

namespace Cafe\ReadModel\OpenTabs;

use Cafe\Aggregate\Events\DrinksOrdered;
use Cafe\Aggregate\Events\DrinksServed;
use Cafe\Aggregate\Events\TabClosed;
use Cafe\Aggregate\Events\TabOpened;
use Cafe\Aggregate\OrderedItem;
use PHPUnit\Framework\TestCase;

class OpenTabsTest extends TestCase
{
    private OpenTabs $openTabs;

    protected function setUp() : void
    {
        $this->openTabs = new OpenTabs();
        $this->openTabs->handleTabOpened(new TabOpened('tab-id-1', 1, 'Daniel'));
        $this->openTabs->handleTabOpened(new TabOpened('tab-id-2', 2, 'Rachel'));
    }

    /**
     * @test
     */
    public function activeTableNumbers() : void
    {
        self::assertEquals([1,2], $this->openTabs->activeTableNumbers());
    }

    /**
     * @test
     */
    public function tabIdForTable() : void
    {
        self::assertEquals('tab-id-2', $this->openTabs->tabIdForTable(2));
    }

    /**
     * @test
     */
    public function handleTabClosed() : void
    {
        $this->openTabs->handleTabClosed(new TabClosed('tab-id-1', 100, 100, 0));
        self::assertEquals([2], $this->openTabs->activeTableNumbers());
    }

    /**
     * @test
     */
    public function handleDrinksOrdered() : void
    {
        $this->openTabs->handleDrinksOrdered(new DrinksOrdered('tab-id-1', [
            new OrderedItem(1, 'Soda', true, 10),
            new OrderedItem(2, 'Cola', true, 10),
            new OrderedItem(3, 'Beer', true, 10),
            new OrderedItem(4, 'Wine', true, 10),
        ]));

        $tabStatus = $this->openTabs->tabStatusForTable(1);
        self::assertCount(4, $tabStatus->toServe);
    }

    /**
     * @test
     */
    public function handleDrinksServed() : void
    {
        $this->openTabs->handleDrinksOrdered(new DrinksOrdered('tab-id-1', [
            new OrderedItem(1, 'Soda', true, 10),
            new OrderedItem(2, 'Cola', true, 10),
            new OrderedItem(3, 'Beer', true, 10),
            new OrderedItem(4, 'Wine', true, 10),
        ]));

        $this->openTabs->handleDrinksServed(new DrinksServed('tab-id-1', [1,3]));

        self::assertCount(2, $this->openTabs->todoByTab['tab-id-1']->toServe);
        self::assertCount(2, $this->openTabs->todoByTab['tab-id-1']->served);
    }
}

