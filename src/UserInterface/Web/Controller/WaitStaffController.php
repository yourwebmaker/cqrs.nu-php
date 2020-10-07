<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Cafe\Application\ReadModel\OpenTabs\OpenTabQueries;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class WaitStaffController extends AbstractController
{
    private OpenTabQueries $query;

    public function todo(string $waiter) : Response
    {
        return $this->render('wait_staff/todo.html.twig', [
            'waiter' => $waiter,
            'model' => $this->query->todoListForWaiter($waiter)
        ]);
    }
}