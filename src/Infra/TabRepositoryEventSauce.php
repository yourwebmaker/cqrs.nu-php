<?php

declare(strict_types=1);

namespace Cafe\Infra;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabId;
use Cafe\Domain\Tab\TabRepository;
use EventSauce\EventSourcing\AggregateRootRepository;

use function assert;

class TabRepositoryEventSauce implements TabRepository
{
    public function __construct(private AggregateRootRepository $repository)
    {
    }

    public function save(Tab $tab): void
    {
        $this->repository->persist($tab);
    }

    public function get(string $tabId): Tab
    {
        $tab = $this->repository->retrieve(TabId::fromString($tabId));
        assert($tab instanceof Tab);// todo use annonymous class

        return $tab;
    }
}
