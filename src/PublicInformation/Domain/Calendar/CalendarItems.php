<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\Calendar;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class CalendarItems implements IteratorAggregate
{
    /**
     * @var CalendarItem[]
     */
    private array $compactCalendarItems;

    /**
     * @param CalendarItem[] $compactCalendarItems
     *
     * @return static
     */
    public static function fromArray(array $compactCalendarItems): self
    {
        Assertion::allIsInstanceOf($compactCalendarItems, CalendarItem::class);

        $self                       = new self();
        $self->compactCalendarItems = $compactCalendarItems;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->compactCalendarItems);
    }

    private function __construct()
    {
    }
}
