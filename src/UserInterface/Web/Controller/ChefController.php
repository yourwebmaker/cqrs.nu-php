<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Cafe\Application\Read\ChefTodoListQueries;
use Cafe\Application\Write\MarkFoodPrepared;
use Cafe\Domain\Tab\TabId;
use Cafe\Domain\Tab\TabRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ChefController extends AbstractController
{
    private ChefTodoListQueries $query;
    private TabRepository $repository;

    public function __construct(ChefTodoListQueries $query, TabRepository $repository)
    {
        $this->query = $query;
        $this->repository = $repository;
    }

    /**
     * @Route(path="/chef", name="chef_index")
     */
    public function index() : Response
    {
        return $this->render('chef/index.html.twig', [
            'groups' => $this->query->getTodoList(),
        ]);
    }

    /**
     * @Route(path="/chef/markprepared", name="chef_markprepared")
     */
    public function markPrepared(Request $request) : RedirectResponse
    {
        //todo... jesus... fix this mess.
        $menuNumbers = array_map(fn(array $item) => array_key_first($item), $request->request->get('items'));
        $tabIdString = $request->request->getAlnum('tabId');
        $tabIdVO = TabId::fromString($tabIdString);
        $tab = $this->repository->get($tabIdVO);
        $command = new MarkFoodPrepared($tabIdString, $menuNumbers);
        $tab->markFoodPrepared($command);
        $this->repository->save($tab);

        return $this->redirectToRoute('chef_index');
    }
}