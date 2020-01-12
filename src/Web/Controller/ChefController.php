<?php

declare(strict_types=1);

namespace Cafe\Web\Controller;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

final class ChefController
{
    private $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function index() : Response
    {
        return new Response($this->twig->render('index.html', ['name' => 'Fabien']));
    }

    public function markAsPrepared() : Response
    {
        return new Response('Mark as prepared');
    }
}