<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class Holidays implements IteratorAggregate
{
    /**
     * @var Holiday[]
     */
    private array $holidays;

    /**
     * @param Holiday[] $holidays
     *
     * @return static
     */
    public static function fromArray(array $holidays): self
    {
        Assertion::allIsInstanceOf($holidays, Holiday::class);

        $self           = new self();
        $self->holidays = $holidays;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->holidays);
    }

    private function __construct()
    {
    }
}
