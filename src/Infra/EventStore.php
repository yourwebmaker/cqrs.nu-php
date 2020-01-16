<?php

declare(strict_types=1);

namespace Cafe\Infra;

use Cafe\Aggregate\Events\DomainEvent;
use Cafe\Aggregate\Events\TabOpened;
use Doctrine\DBAL\Connection;
use RuntimeException;

final class EventStore
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $tabId
     * @return DomainEvent[]
     */
    public function getEventsForAggregate(string $tabId) : array
    {
        $sql = 'select * from tab_events where aggregate_id = :tabId';
        $eventsFromDb = $this->connection->fetchAll($sql, ['tabId' => $tabId]);
        $events = [];
        foreach ($eventsFromDb as $item) {
            $payload = json_decode($item['payload'], true, 512, JSON_THROW_ON_ERROR);
            switch ($item['type']) {
                case 'tab_opened':
                    $event = TabOpened::fromPayload($payload);
                    break;
                default:
                    throw new RuntimeException('Not possible to create domain event from ' . $item['type']);
            }

            $events[] = $event;
        }

        return $events;
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