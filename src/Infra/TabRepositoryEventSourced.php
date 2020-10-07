<?php

declare(strict_types=1);

namespace Cafe\Infra;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;

final class TabRepositoryEventSourced// implements TabRepository
{
    private EventStore $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function save(Tab $tab): void
    {
        $this->eventStore->appendEvents($tab->getRecordedEvents());
    }

    public function get(string $tabId): Tab
    {
        $events = $this->eventStore->getEventsForAggregate($tabId);
        return Tab::fromEvents($events);
    }
}