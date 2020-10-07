<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use EventSauce\EventSourcing\AggregateRootId;

class TabId implements AggregateRootId
{
    private string $identifier;

    private function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function toString(): string
    {
        return $this->identifier;
    }

    public static function fromString(string $aggregateRootId): self
    {
        return new self($aggregateRootId);
     }
}