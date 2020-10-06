<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class HomeController
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index() : Response
    {
        return new Response($this->twig->render('index/index.twig'));
    }
}