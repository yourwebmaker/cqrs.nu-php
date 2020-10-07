<?php

declare(strict_types=1);

namespace Cafe\Infra\ServiceFactory;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;
use Cafe\Infra\TabRepositoryEventSauce;
use Doctrine\DBAL\DriverManager;
use EventSauce\DoctrineMessageRepository\DoctrineMessageRepository;
use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;

class TabRepositoryFactory
{
    public function create() : TabRepository
    {
        return new TabRepositoryEventSauce(
            new ConstructingAggregateRootRepository(
                Tab::class,
                new DoctrineMessageRepository(
                    DriverManager::getConnection([
                        'driver' => 'pdo_sqlite',
                        'path' => __DIR__ . '/../../../var/data/db.sqlite'
                    ]),
                    new ConstructingMessageSerializer(),
                    'tab_aggregate'
                )
            )
        );
    }
}