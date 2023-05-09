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

use function assert;
use function is_int;

class ChefTodoProjector implements Consumer
{
    public function __construct(private Connection $connection)
    {
    }

    public function handle(Message $message): void
    {
        $event = $message->event();

        if ($event instanceof FoodOrdered) {
            $groupId = Uuid::uuid4()->toString();

            //todo ... wrap within transaction
            $this->connection->insert('read_model_chef_todo_group', [
                'tab_id' => $event->tabId,
                'group_id' => $groupId,
            ]);

            foreach ($event->items as $item) {
                assert($item instanceof OrderedItem);
                $this->connection->insert('read_model_chef_todo_item', [
                    'tab_id' => $event->tabId,
                    'group_id' => $groupId,
                    'description' => $item->description,
                    'menu_number' => $item->menuNumber,
                ]);
            }
        }

        if (! ($event instanceof FoodPrepared)) {
            return;
        }

        foreach ($event->menuNumbers as $menuNumber) {
            assert(is_int($menuNumber));
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
