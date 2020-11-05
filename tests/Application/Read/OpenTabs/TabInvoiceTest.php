<?php

declare(strict_types=1);


namespace Cafe\Application\Read\OpenTabs;

use Cafe\Application\Read\OpenTabs\Invoice\Line;
use PHPUnit\Framework\TestCase;

class TabInvoiceTest extends TestCase
{
    private TabInvoice $invoice;
    private int $tableNumber = 123;

    public function setUp() : void
    {
        $items = [
            new TabItem(1, 'coca-cola', 2.50, TabItem::STATUS_SERVED),
            new TabItem(2, 'coca-cola LIGHT', 2.75, TabItem::STATUS_SERVED),
            new TabItem(2, 'coca-cola LIGHT', 2.75, TabItem::STATUS_SERVED),
            new TabItem(3, 'Stroganoff', 10.75, TabItem::STATUS_IN_PREPARATION)
        ];

        $this->invoice = new TabInvoice('tabId', $this->tableNumber, $items);
    }

    /**
     * @test
     */
    public function get_total() : void
    {
        self::assertEquals(8.00, $this->invoice->getTotal());
    }

    /**
     * @test
     */
    public function get_total_sums_only_served_items(): void
    {
        self::assertEquals(8.00, $this->invoice->getTotal());
    }

    /**
     * @test
     */
    public function get_lines(): void
    {
        $lines = [
            new Line('coca-cola', 1, 2.50, 2.50),
            new Line('coca-cola LIGHT', 2, 2.75, 5.50),
        ];

        self::assertEquals($lines,  $this->invoice->getLines());
    }

    /**
     * @test
     */
    public function has_unserved_items() : void
    {
        self::assertTrue($this->invoice->hasUnservedItems());
    }
}
