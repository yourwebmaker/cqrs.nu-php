<?php

declare(strict_types=1);

namespace Cafe\Application;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;

final class TabHandler
{
    private TabRepository $repository;

    public function __construct(TabRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handleOpenTab(OpenTabCommand $command) : void
    {
        $tab = Tab::open($command->tabId, $command->tableNumber, $command->waiter);
        $this->repository->save($tab);
    }
}