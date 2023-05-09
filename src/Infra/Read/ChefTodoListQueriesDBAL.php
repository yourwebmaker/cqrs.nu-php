<?php

declare(strict_types=1);

namespace Cafe\Infra\Read;

use Cafe\Application\Read\ChefTodoList\TodoListGroup;
use Cafe\Application\Read\ChefTodoList\TodoListItem;
use Cafe\Application\Read\ChefTodoListQueries;
use Doctrine\DBAL\Connection;

class ChefTodoListQueriesDBAL implements ChefTodoListQueries
{
    public function __construct(private Connection $connection)
    {
    }

    public function getTodoList(): array
    {
        $groups    = $itemsByGroupId = [];
        $rowsItems = $this->connection->fetchAllAssociative('select * from read_model_chef_todo_item');

        foreach ($rowsItems as $i => $row) {
            $itemsByGroupId[$row['group_id']][$i] = $row;
        }

        foreach ($itemsByGroupId as $groupId => $itemRows) {
            $items = [];
            foreach ($itemRows as $i => $itemRow) {
                $items[] = new TodoListItem((int) $itemRow['menu_number'], $itemRow['description']);
            }

            $groups[] = new TodoListGroup($groupId, $itemRows[$i]['tab_id'], $items);
        }

        return $groups;
    }
}
