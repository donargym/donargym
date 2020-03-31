<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class Lessons implements IteratorAggregate
{
    /**
     * @var Lesson[]
     */
    private array $lessons;

    /**
     * @param Lesson[] $lessons
     *
     * @return static
     */
    public static function fromArray(array $lessons): self
    {
        Assertion::allIsInstanceOf($lessons, Lesson::class);

        $self          = new self();
        $self->lessons = $lessons;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->lessons);
    }

    private function __construct()
    {
    }
}
