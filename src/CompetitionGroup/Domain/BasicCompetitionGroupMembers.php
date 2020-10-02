<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class BasicCompetitionGroupMembers implements IteratorAggregate
{
    /**
     * @var BasicCompetitionGroupMember[]
     */
    private array $competitionGroupMembers;

    /**
     * @param BasicCompetitionGroupMember[] $competitionGroupMembers
     *
     * @return static
     */
    public static function fromArray(array $competitionGroupMembers): self
    {
        Assertion::allIsInstanceOf($competitionGroupMembers, BasicCompetitionGroupMember::class);
        $self                          = new self();
        $self->competitionGroupMembers = $competitionGroupMembers;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->competitionGroupMembers);
    }

    private function __construct()
    {
    }
}
