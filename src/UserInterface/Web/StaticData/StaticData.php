<?php

declare(strict_types=1);

namespace Cafe\UserInterface\Web\StaticData;

final class StaticData
{
    public static function getMenu(): array
    {
        return [
            1 => new MenuItem(1, 'Coke', 1.50, true),
            2 => new MenuItem(2, 'Green Tea', 1.90, true),
            3 => new MenuItem(3, 'Freshly Ground Coffee', 2.00, true),
            4 => new MenuItem(4, 'Czech Pilsner', 3.50, true),
            5 => new MenuItem(5, 'Yeti Stout', 4.50, true),
            10 => new MenuItem(10, 'Mushroom & Bacon Pasta', 6.00),
            11 => new MenuItem(11, 'Chili Con Carne', 7.50),
            12 => new MenuItem(12, 'Borsch With Smetana', 4.50),
            13 => new MenuItem(13, 'Lamb Skewers with Tatziki', 8.00),
            14 => new MenuItem(14, 'Beef Stroganoff', 8.50),
        ];
    }

    public static function getWaitStaff(): array
    {
        return [
            'Jack' => 'Jack',
            'Lena' => 'Lena',
            'Pedro' => 'Pedro',
            'Anastasia' => 'Anastasia',
        ];
    }
}
