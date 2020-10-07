<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabId;
use Cafe\Domain\Tab\TabRepository;
use Cafe\UserInterface\Web\Form\CloseTabType;
use Cafe\UserInterface\Web\Form\OpenTabType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TabController extends AbstractController
{
    private TabRepository $repository;

    public function __construct(TabRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @Route(path="tab/open", name="tab_open")
     */
    public function open(Request $request): Response
    {
        $form = $this->createForm(OpenTabType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tab = Tab::open(
                TabId::fromString('tab-id'),
                $form->get('tableNumber')->getData(),
                $form->get('waiter')->getData(),
            );

            $this->repository->save($tab);

            return $this->redirectToRoute('home');
        }

        return $this->render('tab/open.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="tab/order", name="tab_order")
     */
    public function order(Request $request) : Response
    {
        return $this->render('tab/order.html.twig', [
            //'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="tab/close", name="tab_close")
     */
    public function close(Request $request): Response
    {
        $form = $this->createForm(CloseTabType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('tab/close.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}