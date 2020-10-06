<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab;

use Cafe\Domain\Tab\Events\DomainEvent;

abstract class BaseAggregate
{
    private $recordedEvents;

    public function recordEvent($event) : void
    {
        $this->recordedEvents[] = $event;
    }

    public function getRecordedEvents() : array
    {
        return $this->recordedEvents;
    }

    abstract protected function apply(DomainEvent $domainEvent) : void;
}