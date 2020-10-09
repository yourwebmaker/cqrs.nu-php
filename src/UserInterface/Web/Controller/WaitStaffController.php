<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WaitStaffController extends AbstractController
{
    /**
     * @Route(name="waitstaff_todo", path="waitstaff/{id}/todo")
     */
    public function todo(string $id) : Response
    {
        return $this->render('wait_staff/todo.html.twig', [
            'waiter' => $id,
            //'model' => $this->query->todoListForWaiter($id)
        ]);
    }
}