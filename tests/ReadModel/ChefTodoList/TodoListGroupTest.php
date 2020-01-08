<?php

declare(strict_types=1);

namespace Cafe\ReadModel\ChefTodoList;

use PHPUnit\Framework\TestCase;

class TodoListGroupTest extends TestCase
{
    /**
     * @test
     */
    public function removeItem(): void
    {
        $group = new TodoListGroup('tab-id-1', [
            new TodoListItem(1, 'Pasta Carbonara'),
            new TodoListItem(2, 'Pasta Bologna'),
        ]);

        $group->removeItem(1);

        self::assertCount(1, $group->items);
    }
}

