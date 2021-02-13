<?php

declare(strict_types=1);

namespace Cafe\Infra;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;
use EventSauce\EventSourcing\AggregateRootRepository;
use EventSauce\EventSourcing\UuidAggregateRootId;

use function assert;

class TabRepositoryEventSauce implements TabRepository
{
    private AggregateRootRepository $repository;

    public function __construct(AggregateRootRepository $repository)
    {
        $this->repository = $repository;
    }

    public function save(Tab $tab): void
    {
        $this->repository->persist($tab);
    }

    public function get(string $tabId): Tab
    {
        $tab = $this->repository->retrieve(UuidAggregateRootId::fromString($tabId));
        assert($tab instanceof Tab);// todo use annonymous class

        return $tab;
    }
}
