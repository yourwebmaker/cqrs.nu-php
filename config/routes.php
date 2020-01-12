<?php

declare(strict_types=1);

use Cafe\Web\Controller\HomeController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
$twig = new \Twig\Environment($loader, [
    //'cache' => '/path/to/compilation_cache',
]);
$twig->addGlobal('staff', \Cafe\Web\StaticData\StaticData::getWaitStaff());
$twig->addGlobal('menu', \Cafe\Web\StaticData\StaticData::getMenu());
$controller = new \Cafe\Web\Controller\ChefController($twig);

$routes = new RouteCollection();
$routes->add('index', new Route('/', [
    '_controller' => fn (Request $request) =>  (new HomeController($twig))->index()
]));

$routes->add('chef_index', new Route('/chef', [
    '_controller' => fn (Request $request) =>  $controller->index()
]));

$routes->add('chef_mark_as_prepared', new Route('/chef/mark-as-prepared', [
        '_controller' => 'Cafe\Web\Controller\ChefController::markAsPrepared']
));

return $routes;