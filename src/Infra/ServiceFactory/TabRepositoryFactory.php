<?php

declare(strict_types=1);

namespace Cafe\Infra\ServiceFactory;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabRepository;
use Cafe\Infra\Read\ChefTodoProjector;
use Cafe\Infra\Read\TabProjector;
use Cafe\Infra\TabRepositoryEventSauce;
use Doctrine\DBAL\Connection;
use EventSauce\MessageRepository\DoctrineMessageRepository\DoctrineUuidV4MessageRepository;
use EventSauce\MessageRepository\TableSchema\DefaultTableSchema;
use EventSauce\UuidEncoding\BinaryUuidEncoder;
use EventSauce\EventSourcing\ConstructingAggregateRootRepository;
use EventSauce\EventSourcing\Serialization\ConstructingMessageSerializer;
use EventSauce\EventSourcing\SynchronousMessageDispatcher;

class TabRepositoryFactory
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function create(): TabRepository
    {
        return new TabRepositoryEventSauce(
            new ConstructingAggregateRootRepository(
                Tab::class,
                new DoctrineUuidV4MessageRepository(
                    connection: $this->connection,
                    tableName: 'aggregate_tab',
                    serializer: new ConstructingMessageSerializer(),
                ),
                new SynchronousMessageDispatcher(
                    new TabProjector($this->connection),
                    new ChefTodoProjector($this->connection),
                )
            )
        );
    }
}
