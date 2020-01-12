<?php

declare(strict_types=1);

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../views');
$twig = new \Twig\Environment($loader, [
    //'cache' => '/path/to/compilation_cache',
]);
$controller = new \Cafe\Web\Controller\ChefController($twig);

$routes = new RouteCollection();
$routes->add('chef_index', new Route('/chef', [
    '_controller' => static function (\Symfony\Component\HttpFoundation\Request $request) use ($controller){
        return $controller->index($request);
    }]
));
$routes->add('chef_mark_as_prepared', new Route('/chef/mark-as-prepared', [
        '_controller' => 'Cafe\Web\Controller\ChefController::markAsPrepared']
));

return $routes;