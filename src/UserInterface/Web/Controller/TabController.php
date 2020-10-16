<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Cafe\Application\Read\OpenTabsQueries;
use Cafe\Application\Write\MarkDrinksServed;
use Cafe\Application\Write\MarkItemsServedCommand;
use Cafe\Application\Write\OpenTabCommand;
use Cafe\Application\Write\PlaceOrderCommand;
use Cafe\Application\Write\TabHandler;
use Cafe\Domain\Tab\OrderedItem;
use Cafe\Domain\Tab\Tab;
use Cafe\Domain\Tab\TabId;
use Cafe\Domain\Tab\TabRepository;
use Cafe\UserInterface\Web\Form\CloseTabType;
use Cafe\UserInterface\Web\Form\OpenTabType;
use Cafe\UserInterface\Web\Form\OrderType;
use Cafe\UserInterface\Web\Model\OrderItem;
use Cafe\UserInterface\Web\Model\OrderModel;
use Cafe\UserInterface\Web\StaticData\MenuItem;
use Cafe\UserInterface\Web\StaticData\StaticData;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TabController extends AbstractController
{
    private TabRepository $repository;
    private OpenTabsQueries $queries;
    private TabHandler $handler;

    public function __construct(TabRepository $repository, OpenTabsQueries $queries, TabHandler $handler)
    {
        $this->repository = $repository;
        $this->queries = $queries;
        $this->handler = $handler;
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

            $tab = Tab::open(
                new OpenTabCommand(
                    Uuid::uuid4()->toString(),
                    $tableNumber,
                    $form->get('waiter')->getData(),
                )
            );

            $this->repository->save($tab);

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

        $items = array_map(fn(MenuItem $menuItem) => new OrderItem(
            $menuItem->menuNumber,
            $menuItem->description,
            0
        ), $menu);

        $orderModel = new OrderModel($items);
        $form = $this->createForm(OrderType::class, $orderModel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderedItems = [];
            foreach ($orderModel->items as $item) {
                for ($i = 0; $i < $item->numberToOrder; $i++) {
                    $orderedItems[] = new OrderedItem(
                        $item->menuNumber,
                        $menu[$item->menuNumber]->description,
                        $menu[$item->menuNumber]->isDrink,
                        $menu[$item->menuNumber]->price,
                    );
                }
            }

            $tabId = $this->queries->tabIdForTable($tableNumber);
            $command = new PlaceOrderCommand($tabId, $orderedItems);
            $tab = $this->repository->get(TabId::fromString($tabId));

            $tab->order($command);
            $this->repository->save($tab);

            return $this->redirectToRoute('tab_status', ['tableNumber' => $tableNumber]);
        }

        return $this->render('tab/order.html.twig', [
            'form' => $form->createView(),
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
        $menuNumbers = array_map(fn(array $item) => array_key_first($item), $request->request->get('items'));
        $tabId = $this->queries->tabIdForTable($tableNumber);
        $command = new MarkItemsServedCommand($tabId, $menuNumbers);
        $this->handler->markServed($command);

        return $this->redirectToRoute('tab_status', ['tableNumber' => $tableNumber]);
    }

    /**
     * @Route(path="tab/{tableNumber}/close", name="tab_close")
     */
    public function close(string $tableNumber, Request $request): Response
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