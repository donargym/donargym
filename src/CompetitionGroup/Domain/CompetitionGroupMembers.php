<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class CompetitionGroupMembers implements IteratorAggregate
{
    /**
     * @var CompetitionGroupMember[]
     */
    private array $competitionGroupMembers;

    /**
     * @param CompetitionGroupMember[] $competitionGroupMembers
     *
     * @return static
     */
    public static function fromArray(array $competitionGroupMembers): self
    {
        Assertion::allIsInstanceOf($competitionGroupMembers, CompetitionGroupMember::class);
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
