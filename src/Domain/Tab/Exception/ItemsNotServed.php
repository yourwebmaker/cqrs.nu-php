<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Exception;

use DomainException;

final class ItemsNotServed extends DomainException
{
    public static function withTotals(int $notServedTotal) : self
    {
        return new self("Cannot close tab with a total {$notServedTotal} of items not served");
    }
}