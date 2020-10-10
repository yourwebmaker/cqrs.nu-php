<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Application\Read\OpenTabs\Tab;
use Cafe\Domain\Tab\Events\TabOpened;
use Doctrine\ORM\EntityManagerInterface;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;

class TabProjector implements Consumer
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function handle(Message $message) : void
    {
        $event = $message->event();

        if ($event instanceof TabOpened) {
            $this->entityManager->persist(new Tab($event->tabId->toString(), $event->tableNumber, $event->waiter, [], [], []));
        }

        $this->entityManager->flush();
    }
}