<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TabController extends AbstractController
{
    /**
     * @Route(path="tab/open", name="tab_open")
     */
    public function open(): Response
    {
        return $this->render('tab/open.html.twig');
    }
}