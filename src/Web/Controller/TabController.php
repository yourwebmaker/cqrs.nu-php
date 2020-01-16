<?php

declare(strict_types=1);

namespace Cafe\Web\Controller;

use Cafe\Application\OpenTabCommand;
use Cafe\Application\TabHandler;
use Cafe\Infra\EventStore;
use Cafe\Infra\TabRepositoryEventSourced;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class TabController
{
    private CommandBus $commandBus;
    private Environment $twig;

    public function __construct(CommandBus $commandBus, Environment $twig)
    {
        $this->commandBus = $commandBus;
        $this->twig = $twig;
    }

    public function open() : Response
    {
        $conn = \Doctrine\DBAL\DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../../data/db.sqlite'
        ]);

        $repo = new TabRepositoryEventSourced(
            new EventStore($conn)
        );

        var_dump($repo->get('20044962-cefe-40a7-a9b2-a5bb1e9ca7e8'));
        exit;

        return new Response($this->twig->render('tab/open.twig'));
    }

    public function openPost(Request $request) : Response
    {
        $command = new OpenTabCommand(
            Uuid::uuid4()->toString(),
            $request->request->getInt('tableNumber'),
            $request->get('waiter')
        );

        $conn = \Doctrine\DBAL\DriverManager::getConnection([
            'driver' => 'pdo_sqlite',
            'path' => __DIR__ . '/../../../data/db.sqlite'
        ]);
        $handler = new TabHandler(new TabRepositoryEventSourced(
            new EventStore($conn)
        ));

        $handler->handleOpenTab($command);

        //$this->commandBus->handle($command);

        return new RedirectResponse('/tab/order/' . $command->tableNumber);
    }

    public function order() : Response
    {
        return new Response();
    }
}