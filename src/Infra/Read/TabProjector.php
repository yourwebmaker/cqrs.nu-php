<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Domain\Tab\Events\DrinksOrdered;
use Cafe\Domain\Tab\Events\DrinksServed;
use Cafe\Domain\Tab\Events\FoodOrdered;
use Cafe\Domain\Tab\Events\FoodPrepared;
use Cafe\Domain\Tab\Events\FoodServed;
use Cafe\Domain\Tab\Events\TabClosed;
use Cafe\Domain\Tab\Events\TabOpened;
use Cafe\Domain\Tab\OrderedItem;
use Doctrine\DBAL\Connection;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;

use function assert;

class TabProjector implements Consumer
{
    public function __construct(private Connection $connection)
    {
    }

    public function handle(Message $message): void
    {
        $event = $message->event();

        if ($event instanceof TabOpened) {
            $this->connection->insert('read_model_tab', [
                'tab_id' => $event->tabId,
                'table_number' => $event->tableNumber,
                'waiter' => $event->waiter,
            ]);
        }

        if ($event instanceof DrinksOrdered) {
            foreach ($event->items as $item) {
                assert($item instanceof OrderedItem);
                $this->connection->insert('read_model_tab_item', [
                    'tab_id' => $event->tabId,
                    'menu_number' => $item->menuNumber,
                    'description' => $item->description,
                    'price' => $item->price,
                    'status' => 'to-serve',
                ]);
            }
        }

        if ($event instanceof DrinksServed) {
            foreach ($event->menuNumbers as $menuNumber) {
                $sql = '
                    update read_model_tab_item 
                    set status = :new_status 
                    where 
                        menu_number = :menu_number and
                        tab_id = :tab_id and
                        status = :old_status
                    limit 1';
                $this->connection->executeQuery($sql, [
                    'new_status' => 'served',
                    'old_status' => 'to-serve',
                    'menu_number' => $menuNumber,
                    'tab_id' => $event->tabId,
                ]);
            }
        }

        if ($event instanceof FoodOrdered) {
            foreach ($event->items as $item) {
                assert($item instanceof OrderedItem);
                $this->connection->insert('read_model_tab_item', [
                    'tab_id' => $event->tabId,
                    'menu_number' => $item->menuNumber,
                    'description' => $item->description,
                    'price' => $item->price,
                    'status' => 'in-preparation',
                ]);
            }
        }

        if ($event instanceof FoodPrepared) {
            foreach ($event->menuNumbers as $menuNumber) {
                $sql = '
                    update read_model_tab_item 
                    set status = :new_status 
                    where 
                        menu_number = :menu_number and
                        tab_id = :tab_id and
                        status = :old_status
                    limit 1';
                $this->connection->executeQuery($sql, [
                    'old_status' => 'in-preparation',
                    'new_status' => 'to-serve',
                    'menu_number' => $menuNumber,
                    'tab_id' => $event->tabId,
                ]);
            }
        }

        if ($event instanceof FoodServed) {
            foreach ($event->menuNumbers as $menuNumber) {
                $sql = '
                    update read_model_tab_item 
                    set status = :new_status 
                    where 
                        menu_number = :menu_number and
                        tab_id = :tab_id and
                        status = :old_status
                    limit 1';
                $this->connection->executeQuery($sql, [
                    'old_status' => 'to-serve',
                    'new_status' => 'served', //use constants here.
                    'menu_number' => $menuNumber,
                    'tab_id' => $event->tabId,
                ]);
            }
        }

        if (! ($event instanceof TabClosed)) {
            return;
        }

        $tables = ['read_model_chef_todo_group', 'read_model_chef_todo_item', 'read_model_tab', 'read_model_tab_item'];
        foreach ($tables as $table) {
            $sql = 'delete from ' . $table . ' where tab_id = :tab_id';
            $this->connection->executeQuery($sql, ['tab_id' => $event->tabId]);
        }
    }
}
