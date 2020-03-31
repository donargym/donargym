<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class CompactCalendarItems implements IteratorAggregate
{
    /**
     * @var CompactCalendarItem[]
     */
    private array $compactCalendarItems;

    /**
     * @param CompactCalendarItem[] $compactCalendarItems
     *
     * @return static
     */
    public static function fromArray(array $compactCalendarItems): self
    {
        Assertion::allIsInstanceOf($compactCalendarItems, CompactCalendarItem::class);

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
