<?php

declare(strict_types=1);

namespace Cafe\Infra;

use Cafe\Aggregate\Events\DomainEvent;
use Doctrine\DBAL\Connection;

final class EventStore
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getEventsForAggregate(string $tabId) : array
    {
        return [];
    }

    /**
     * @param DomainEvent[] $events
     */
    public function appendEvents(array $events) : void
    {
        foreach ($events as $event) {
            $this->connection->insert('tab_events', [
                'aggregate_id' => $event->aggregateId(),
                'type' => $event->name(),
                'payload' => $event->payload(),
                'occurred_on' => $event->occurredOn()->format('Y-d-m H:i:s'),
            ]);
        }
    }
}