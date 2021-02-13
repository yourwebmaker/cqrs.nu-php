<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Cafe\Application\Read\ChefTodoListQueries;
use Cafe\Application\Write\MarkFoodPreparedCommand;
use League\Tactician\CommandBus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function array_map;

final class ChefController extends AbstractController
{
    private ChefTodoListQueries $query;
    private CommandBus $commandBus;

    public function __construct(ChefTodoListQueries $query, CommandBus $commandBus)
    {
        $this->query      = $query;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route(path="/chef", name="chef_index")
     */
    public function index(): Response
    {
        return $this->render('chef/index.html.twig', [
            'groups' => $this->query->getTodoList(),
        ]);
    }

    /**
     * @Route(path="/chef/markprepared", name="chef_markprepared")
     */
    public function markPrepared(Request $request): RedirectResponse
    {
        $menuNumbers = array_map(static fn (string $itemString) => (int) $itemString, $request->request->get('items'));
        $tabIdString = $request->request->get('tabId');
        $groupId     = $request->request->get('groupId');

        $this->commandBus->handle(new MarkFoodPreparedCommand($tabIdString, $groupId, $menuNumbers));

        return $this->redirectToRoute('chef_index');
    }
}
