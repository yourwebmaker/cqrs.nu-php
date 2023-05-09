<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;

final class HomeController extends AbstractController
{
    /** @Route(name="home", path="/") */
    public function index(RouterInterface $router): Response
    {
        return $this->render('home/home.html.twig', ['open_tab_url' => $router->generate('tab_open_get')]);
    }
}
