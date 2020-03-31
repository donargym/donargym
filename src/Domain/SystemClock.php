<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use DateTimeZone;

final class SystemClock
{
    public function now(DateTimeZone $timeZone = null): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $timeZone);
    }
}
