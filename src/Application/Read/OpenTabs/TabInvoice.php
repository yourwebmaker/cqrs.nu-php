<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use Cafe\Application\Read\OpenTabs\Invoice\Line;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

final class TabInvoice
{
    public string $tabId;
    public int $tableNumber;
    /** @var Collection<TabItem> */
    public Collection $items;

    public function __construct(string $tabId, int $tableNumber, array $items)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->items = new ArrayCollection($items);
    }

    public function getLines(): array
    {
        $groupedItems = [];
        /** @var TabItem $item */
        foreach ($this->items as $item) {
            if ($item->status === TabItem::STATUS_SERVED) {
                $groupedItems[$item->menuNumber][] = $item;
            }
        }

        $lines = [];
        foreach ($groupedItems as $menuNumber => $groupedItem) {
            foreach ($groupedItem as $item) {
                $lines[$menuNumber] = new Line(
                    $item->description,
                    count($groupedItems[$menuNumber]),
                    $item->price,
                    count($groupedItems[$menuNumber]) * $item->price
                );
            }
        }

        return array_values($lines);
    }

    public function hasUnservedItems(): bool
    {
        return $this->items->filter(fn(TabItem $item) => $item->status !== TabItem::STATUS_SERVED)->count() > 0;
    }

    public function getTotal(): float
    {
        $total = 0.0;
        /** @var TabItem $item */
        foreach ($this->items as $item) {
            if($item->status === TabItem::STATUS_SERVED) {
                $total+= $item->price;
            }
        }

        return $total;
    }
}