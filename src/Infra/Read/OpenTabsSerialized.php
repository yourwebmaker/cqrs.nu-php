<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Application\Read\OpenTabs\Tab;
use Cafe\Application\Read\OpenTabs\TabInvoice;
use Cafe\Application\Read\OpenTabs\TabStatus;
use Cafe\Application\Read\OpenTabsQueries;
use Cafe\Domain\Tab\Events\TabOpened;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;

class OpenTabsSerialized implements OpenTabsQueries, Consumer
{
    /** @var array<string, Tab>>  */
    private array $todoByTab;

    /**
     * @inheritDoc
     */
    public function activeTableNumbers(): array
    {
        return array_map(fn(Tab $tab) => $tab->tableNumber, $this->unserialize());
    }

    public function invoiceForTable(int $table): TabInvoice
    {
        // TODO: Implement invoiceForTable() method.
    }

    public function tabIdForTable(int $table): string
    {
        // TODO: Implement tabIdForTable() method.
    }

    public function tabForTable(int $table): TabStatus
    {
        // TODO: Implement tabForTable() method.
    }

    /**
     * @inheritDoc
     */
    public function todoListForWaiter(string $waiter): array
    {
        // TODO: Implement todoListForWaiter() method.
    }

    public function handle(Message $message) : void
    {
        $event = $message->event();

        if ($event instanceof TabOpened) {
            $this->todoByTab[$event->tabId->toString()] = new Tab($event->tableNumber, $event->waiter, [], [], []);
        }

        $this->serializeTab(); //todo, move the whole shit to doctrine ?
    }

    private function serializeTab() : void
    {
        file_put_contents('tabs', serialize($this->todoByTab));
    }

    /** @return array<string, Tab>>  */
    private function unserialize() : array
    {
        return unserialize(file_get_contents('tabs'));
    }
}