<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use Cafe\Application\Read\OpenTabs\Invoice\Line;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use function array_values;
use function assert;
use function count;

final class TabInvoice
{
    /** @var Collection<TabItem> */
    public Collection $items;

    public function __construct(public string $tabId, public int $tableNumber, array $items)
    {
        $this->items = new ArrayCollection($items);
    }

    public function getLines(): array
    {
        $groupedItems = [];
        foreach ($this->items as $item) {
            assert($item instanceof TabItem);
            if ($item->status !== TabItem::STATUS_SERVED) {
                continue;
            }

            $groupedItems[$item->menuNumber][] = $item;
        }

        $lines = [];
        foreach ($groupedItems as $menuNumber => $groupedItem) {
            foreach ($groupedItem as $item) {
                $lines[$menuNumber] = new Line(
                    $item->description,
                    count($groupedItems[$menuNumber]),
                    $item->price,
                    count($groupedItems[$menuNumber]) * $item->price,
                );
            }
        }

        return array_values($lines);
    }

    public function hasUnservedItems(): bool
    {
        return $this->items->filter(static fn (TabItem $item) => $item->status !== TabItem::STATUS_SERVED)->count() > 0;
    }

    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->items as $item) {
            assert($item instanceof TabItem);
            if ($item->status !== TabItem::STATUS_SERVED) {
                continue;
            }

            $total += $item->price;
        }

        return $total;
    }
}
