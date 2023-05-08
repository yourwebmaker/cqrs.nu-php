<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

final class TabId implements \EventSauce\EventSourcing\AggregateRootId
{
    private function __construct(private string $id)
    {
        $this->id = $id;
    }

    public function toString(): string
    {
        return $this->id;
    }

    public static function fromString(string $aggregateRootId): static
    {
        return new static($aggregateRootId);
    }
}