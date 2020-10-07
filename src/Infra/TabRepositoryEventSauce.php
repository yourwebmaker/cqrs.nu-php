<?php

declare(strict_types=1);

namespace Cafe\Infra;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabId;
use Cafe\Domain\Tab\TabRepository;
use EventSauce\EventSourcing\AggregateRootRepository;

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

    public function get(TabId $tabId): Tab
    {
        /** @var Tab $tab */
        $tab = $this->repository->retrieve($tabId);

        return $tab;
    }

}