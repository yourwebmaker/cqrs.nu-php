<?php

declare(strict_types=1);

namespace Cafe\Web\Controller;

use Cafe\Application\OpenTabCommand;
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
        return new Response($this->twig->render('tab/open.twig'));
    }

    public function openPost(Request $request) : Response
    {
        $command = new OpenTabCommand(
            Uuid::uuid4()->toString(),
            $request->request->getInt('tableNumber'),
            $request->get('waiter')
        );

        $this->commandBus->handle($command);

        return new RedirectResponse('/tab/order/' . $command->tableNumber);
    }

    public function order() : Response
    {
        return new Response();
    }
}