<?php

declare(strict_types=1);

namespace Cafe\Web\StaticData;

final class StaticData
{
    public static function getMenu() : array
    {
        return [
            new MenuItem(1, 'Coke', 1.50, true),
            new MenuItem(2, 'Green Tea', 1.90, true),
            new MenuItem(3, 'Freshly Ground Coffee', 2.00, true),
            new MenuItem(4, 'Czech Pilsner', 3.50, true),
            new MenuItem(5, 'Yeti Stout', 4.50, true),
            new MenuItem(10, 'Mushroom & Bacon Pasta', 6.00),
            new MenuItem(11, 'Chili Con Carne', 7.50),
            new MenuItem(12, 'Borsch With Smetana', 4.50),
            new MenuItem(13, 'Lamb Skewers with Tatziki', 8.00),
            new MenuItem(14, 'Beef Stroganoff', 8.50),
        ];
    }

    public static function getWaitStaff() : array
    {
        return ['Jack', 'Lena', 'Pedro', 'Anastasia'];
    }
}