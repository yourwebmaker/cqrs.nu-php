<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Cafe\Application\Read\OpenTabsQueries;
use Cafe\Application\Write\CloseTabCommand;
use Cafe\Application\Write\MarkItemsServedCommand;
use Cafe\Application\Write\OpenTabCommand;
use Cafe\Application\Write\PlaceOrderCommand;
use Cafe\UserInterface\Web\StaticData\StaticData;
use League\Tactician\CommandBus;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function array_map;

final class TabController extends AbstractController
{
    private OpenTabsQueries $queries;
    private CommandBus $commandBus;

    public function __construct(OpenTabsQueries $queries, CommandBus $commandBus)
    {
        $this->queries    = $queries;
        $this->commandBus = $commandBus;
    }

    /**
     * @Route(path="tab/open", name="tab_open_get", methods={"GET"})
     */
    public function openGet(Request $request): Response
    {
        return $this->render('tab/open.html.twig', [
            'waiters' => StaticData::getWaitStaff(),
        ]);
    }

    /**
     * @Route(path="tab/open", name="tab_open_post", methods={"POST"})
     */
    public function openPost(Request $request): Response
    {
        $tableNumber = $request->request->getInt('tableNumber');
        $this->commandBus->handle(new OpenTabCommand(
            Uuid::uuid4()->toString(),
            $tableNumber,
            $request->request->get('waiter'),
        ));

        return $this->redirectToRoute('tab_order', ['tableNumber' => $tableNumber]);
    }

    /**
     * @Route(path="tab/{tableNumber}/order", name="tab_order")
     */
    public function order(int $tableNumber, Request $request): Response
    {
        $menu = StaticData::getMenu();

        if ($request->isMethod(Request::METHOD_POST)) {
            $orderedItems = array_map(static fn ($value) => $value, $request->request->all('quantity'));
            $tabId        = $this->queries->tabIdForTable($tableNumber);
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
    public function status(int $tableNumber): Response
    {
        return $this->render('tab/status.html.twig', [
            'tableNumber' => $tableNumber,
            'tab' => $this->queries->tabForTable($tableNumber),
        ]);
    }

    /**
     * @Route(path="tab/{tableNumber}/mark-served", name="tab_mark_served")
     */
    public function markServed(int $tableNumber, Request $request)
    {
        $menuNumbers = array_map(static fn (string $itemString) => (int) $itemString, $request->request->all('items'));
        $tabId       = $this->queries->tabIdForTable($tableNumber);
        $this->commandBus->handle(new MarkItemsServedCommand($tabId, $menuNumbers));

        return $this->redirectToRoute('tab_status', ['tableNumber' => $tableNumber]);
    }

    /**
     * @Route(path="tab/{tableNumber}/close", name="tab_close_get", methods={"GET"})
     */
    public function closeGet(int $tableNumber, Request $request): Response
    {
        return $this->render('tab/close.html.twig', [
            'invoice' => $this->queries->invoiceForTable($tableNumber),
        ]);
    }

    /**
     * @Route(path="tab/{tableNumber}/close", name="tab_close_post", methods={"POST"})
     */
    public function closePost(int $tableNumber, Request $request): Response
    {
        $this->commandBus->handle(new CloseTabCommand(
            $this->queries->tabIdForTable($tableNumber),
            (float) $request->request->getDigits('amountPaid')
        ));

        return $this->redirectToRoute('home');
    }
}
