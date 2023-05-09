<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class HomeController extends AbstractController
{
    /** @Route(name="home", path="/") */
    public function index(): Response
    {
        return $this->render('home/home.html.twig');
    }
}
