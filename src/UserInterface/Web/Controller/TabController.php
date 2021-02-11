<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Cafe\Application\Read\OpenTabsQueries;
use Cafe\Application\Write\CloseTabCommand;
use Cafe\Application\Write\MarkItemsServedCommand;
use Cafe\Application\Write\OpenTabCommand;
use Cafe\Application\Write\PlaceOrderCommand;
use Cafe\UserInterface\Web\Form\CloseTabType;
use Cafe\UserInterface\Web\Form\OpenTabType;
use Cafe\UserInterface\Web\StaticData\StaticData;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TabController extends AbstractController
{
    private OpenTabsQueries $queries;
    private CommandBus $commandBus;

    public function __construct(OpenTabsQueries $queries, CommandBus $commandBus)
    {
        $this->queries = $queries;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route(path="tab/open", name="tab_open")
     */
    public function open(Request $request): Response
    {
        $form = $this->createForm(OpenTabType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $tableNumber = $form->get('tableNumber')->getData();

            $this->commandBus->handle(new OpenTabCommand(
                Uuid::uuid4()->toString(),
                $tableNumber,
                $form->get('waiter')->getData(),
            ));

            return $this->redirectToRoute('tab_order', ['tableNumber' => $tableNumber]);
        }

        return $this->render('tab/open.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="tab/{tableNumber}/order", name="tab_order")
     */
    public function order(int $tableNumber, Request $request) : Response
    {
        $menu = StaticData::getMenu();

        if ($request->isMethod(Request::METHOD_POST)) {

            $orderedItems = array_map(fn ($value)  => (int) $value, $request->request->get('quantity'));
            $tabId = $this->queries->tabIdForTable($tableNumber);
            $this->commandBus->handle(new PlaceOrderCommand($tabId, $orderedItems));

            return $this->redirectToRoute('tab_status', ['tableNumber' => $tableNumber]);
        }

        return $this->render('tab/order.html.twig', [
            'menu' => $menu,
            'tableNumber' => $tableNumber,
        ]);
    }

    /**
     * @Route(path="tab/{tableNumber}/status", name="tab_status")
     */
    public function status(int $tableNumber) : Response
    {
        return $this->render('tab/status.html.twig', [
            'tableNumber' => $tableNumber,
            'tab' => $this->queries->tabForTable($tableNumber)
        ]);
    }

    /**
     * @Route(path="tab/{tableNumber}/mark-served", name="tab_mark_served")
     */
    public function markServed(int $tableNumber, Request $request)
    {
        $menuNumbers = array_map(fn(string $itemString) => (int) $itemString, $request->request->get('items'));
        $tabId = $this->queries->tabIdForTable($tableNumber);
        $this->commandBus->handle(new MarkItemsServedCommand($tabId, $menuNumbers));

        return $this->redirectToRoute('tab_status', ['tableNumber' => $tableNumber]);
    }

    /**
     * @Route(path="tab/{tableNumber}/close", name="tab_close")
     */
    public function close(int $tableNumber, Request $request): Response
    {
        $form = $this->createForm(CloseTabType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->commandBus->handle(new CloseTabCommand(
                $this->queries->tabIdForTable($tableNumber),
                $form->get('amountPaid')->getData()
            ));

            return $this->redirectToRoute('home');
        }

        return $this->render('tab/close.html.twig', [
            'form' => $form->createView(),
            'invoice' => $this->queries->invoiceForTable($tableNumber),
        ]);
    }
}