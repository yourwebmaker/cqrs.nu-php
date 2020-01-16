<?php

declare(strict_types=1);

namespace Cafe\Aggregate\Events;

use DateTimeImmutable;

abstract class DomainEvent
{
    abstract public function aggregateId() : string;
    abstract public function name() : string;
    abstract public static function fromPayload(array $payload) : self;

    public function payload() : string
    {
        return json_encode((array) $this, JSON_THROW_ON_ERROR, 512);
    }

    public function occurredOn() : DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
