<?php

declare(strict_types=1);

use Cafe\Web\Controller\ChefController;
use Cafe\Web\Controller\HomeController;
use Cafe\Web\Controller\TabController;
use Cafe\Web\StaticData\StaticData;
use League\Tactician\CommandBus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader(__DIR__ . '/../views');
$twig = new Environment($loader, [
    //'cache' => '/path/to/compilation_cache',
]);
$twig->addGlobal('staff', StaticData::getWaitStaff());
$twig->addGlobal('menu', StaticData::getMenu());
$controller = new ChefController($twig);
$commandBus = new CommandBus([]);
$tabController = new TabController($commandBus, $twig);

$routes = new RouteCollection();
$routes->add('index', new Route('/', [
    '_controller' => fn (Request $request) => (new HomeController($twig))->index()
]));

$routes->add('tab_open', new Route('/tab/open', [
    '_controller' => fn (Request $request) => $tabController->open(),
]));

$routes->add('tab_open_post', new Route('/tab/open/post', [
    '_controller' => fn (Request $request) => $tabController->openPost($request),
]));

$routes->add('tab_order', new Route('/tab/order/{tabId}', [
    '_controller' => fn (Request $request) => $tabController->openPost($request),
]));

$routes->add('chef_index', new Route('/chef', [
    '_controller' => fn (Request $request) =>  $controller->index()
]));

$routes->add('chef_mark_as_prepared', new Route('/chef/mark-as-prepared', [
        '_controller' => 'Cafe\Web\Controller\ChefController::markAsPrepared']
));

return $routes;