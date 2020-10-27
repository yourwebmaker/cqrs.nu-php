<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Domain\Tab\Events\FoodOrdered;
use Cafe\Domain\Tab\Events\FoodPrepared;
use Cafe\Domain\Tab\OrderedItem;
use Doctrine\DBAL\Connection;
use EventSauce\EventSourcing\Consumer;
use EventSauce\EventSourcing\Message;
use Ramsey\Uuid\Uuid;

class ChefTodoProjector implements Consumer
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function handle(Message $message) : void
    {
        $event = $message->event();

        if ($event instanceof FoodOrdered) {
            $groupId = Uuid::uuid4()->toString();

            //todo ... wrap within transaction
            $this->connection->insert('read_model_chef_todo_group', [
                'tab_id' => $event->tabId,
                'group_id' => $groupId
            ]);

            /** @var OrderedItem $item */
            foreach ($event->items as $item) {
                $this->connection->insert('read_model_chef_todo_item', [
                    'tab_id' => $event->tabId,
                    'group_id' => $groupId,
                    'description' => $item->description,
                    'menu_number' => $item->menuNumber,
                ]);
            }
        }

        if ($event instanceof FoodPrepared) {
            /** @var int $menuNumber */
            foreach ($event->menuNumbers as $menuNumber) {
                $sql = 'delete 
                        from read_model_chef_todo_item 
                        where 
                            menu_number = :menu_number and 
                            group_id = :group_id
                        limit 1
                ';

                $this->connection->executeQuery($sql, [
                    'group_id' => $event->groupId,
                    'menu_number' => $menuNumber,
                ]);
            }
        }
    }
}