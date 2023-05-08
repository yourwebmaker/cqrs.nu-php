<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

class TabItem
{
    public const STATUS_TO_SERVE       = 'to-serve';
    public const STATUS_IN_PREPARATION = 'in-preparation';
    public const STATUS_SERVED         = 'served';

    public function __construct(
        public int $menuNumber,
        public string $description,
        public float $price,
        public string $status,
    ) {
    }
}
