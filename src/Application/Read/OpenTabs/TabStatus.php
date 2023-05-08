<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use function count;

final class TabStatus
{
    public function __construct(
        public string $tabId,
        public int $tableNumber,
        public array $toServe,
        public array $inPreparation,
        public array $served,
    ) {
    }

    /** @return array<TabItem> */
    public function getItemsToServe(): array
    {
        return $this->toServe;
    }

    public function hasItemsToServe(): bool
    {
        return count($this->toServe) > 0;
    }

    public function getItemsInPreparation(): array
    {
        return $this->inPreparation;
    }

    public function hasItemsInPreparation(): bool
    {
        return count($this->inPreparation) > 0;
    }

    public function getServedItems(): array
    {
        return $this->served;
    }

    public function hasItemsServed(): bool
    {
        return count($this->served) > 0;
    }
}
