<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class CompetitionGroups implements IteratorAggregate
{
    /**
     * @var CompetitionGroup[]
     */
    private array $competitionGroups;

    /**
     * @param CompetitionGroup[] $competitionGroups
     *
     * @return static
     */
    public static function fromArray(array $competitionGroups): self
    {
        Assertion::allIsInstanceOf($competitionGroups, CompetitionGroup::class);

        $self                    = new self();
        $self->competitionGroups = $competitionGroups;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->competitionGroups);
    }

    private function __construct()
    {
    }
}
