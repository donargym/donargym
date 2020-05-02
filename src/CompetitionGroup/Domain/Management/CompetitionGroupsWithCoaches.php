<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain\Management;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class CompetitionGroupsWithCoaches implements IteratorAggregate
{
    /**
     * @var CompetitionGroupWithCoaches[]
     */
    private array $competitionGroupsWithCoaches;

    /**
     * @param CompetitionGroupWithCoaches[] $competitionGroupsWithCoaches
     *
     * @return static
     */
    public static function fromArray(array $competitionGroupsWithCoaches): self
    {
        Assertion::allIsInstanceOf($competitionGroupsWithCoaches, CompetitionGroupWithCoaches::class);
        $self                               = new self();
        $self->competitionGroupsWithCoaches = $competitionGroupsWithCoaches;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->competitionGroupsWithCoaches);
    }

    private function __construct()
    {
    }
}
