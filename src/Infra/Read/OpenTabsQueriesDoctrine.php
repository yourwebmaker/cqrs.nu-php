<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Application\Read\OpenTabs\Tab;
use Cafe\Application\Read\OpenTabs\TabInvoice;
use Cafe\Application\Read\OpenTabs\TabStatus;
use Cafe\Application\Read\OpenTabsQueries;
use Doctrine\ORM\EntityManagerInterface;

class OpenTabsQueriesDoctrine implements OpenTabsQueries
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @inheritDoc
     */
    public function activeTableNumbers(): array
    {
        //todo replace by simple sql
        $data = $this->entityManager->createQueryBuilder()
            ->from(Tab::class, 't')
            ->select('t')
            ->getQuery()
            ->getResult()
        ;

        return array_map(fn(Tab $tab) => $tab->tableNumber, $data);
    }

    public function invoiceForTable(int $table): TabInvoice
    {
        // TODO: Implement invoiceForTable() method.
    }

    public function tabIdForTable(int $tableNumber): string
    {
        //todo replace by simple sql
        /** @var Tab $tab */
        $tab = $this->entityManager->createQueryBuilder()
            ->from(Tab::class, 't')
            ->select('t')
            ->where('t.tableNumber = :tableNumber')
            ->setParameter('tableNumber', $tableNumber)
            ->getQuery()
            ->getOneOrNullResult();

        return $tab->tabId;
    }

    public function tabForTable(int $table): TabStatus
    {
        return new TabStatus();
    }

    /**
     * @inheritDoc
     */
    public function todoListForWaiter(string $waiter): array
    {
        // TODO: Implement todoListForWaiter() method.
    }
}