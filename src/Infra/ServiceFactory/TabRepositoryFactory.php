<?php

declare(strict_types=1);

namespace Cafe\Infra\ServiceFactory;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;
use Cafe\Infra\Read\ChefTodoProjector;
use Cafe\Infra\Read\TabProjector;
use Cafe\Infra\TabRepositoryEventSauce;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use EventSauce\DoctrineMessageRepository\DoctrineMessageRepository;
use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;

class TabRepositoryFactory
{
    private Connection $connection;
    private EntityManagerInterface $entityManager;

    public function __construct(Connection $connection, EntityManagerInterface $entityManager)
    {
        $this->connection = $connection;
        $this->entityManager = $entityManager;
    }

    public function create() : TabRepository
    {
        return new TabRepositoryEventSauce(
            new ConstructingAggregateRootRepository(
                Tab::class,
                new DoctrineMessageRepository(
                    $this->connection,
                    new ConstructingMessageSerializer(),
                    'aggregate_tab'
                ),
                 new SynchronousMessageDispatcher(
                    new TabProjector($this->entityManager, $this->connection),
                    new ChefTodoProjector($this->connection),
                )
            )
        );
    }
}