<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use EventSauce\EventSourcing\AggregateRootId;
use Ramsey\Uuid\Uuid;

class TabId implements AggregateRootId
{
    private string $identifier;

    private function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public static function generate(): TabId
    {
        return new self(Uuid::uuid4()->toString());
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