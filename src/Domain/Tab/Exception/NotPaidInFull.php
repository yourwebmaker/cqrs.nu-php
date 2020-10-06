<?php

declare(strict_types=1);

namespace Cafe\Domain\Tab\Exception;

use DomainException;

final class NotPaidInFull extends DomainException
{
    public static function withTotals(float $amountPaid, float $totalServed) : self
    {
        return new self("The total of the tab is {$totalServed}, amount paid was {$amountPaid}");
    }
}