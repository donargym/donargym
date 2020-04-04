<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\Competition;

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

    public function count(): int
    {
        return count($this->competitionGroupMembers);
    }

    public function getCoaches(): self
    {
        $competitionGroupMembers = [];
        foreach ($this->competitionGroupMembers as $competitionGroupMember) {
            if ($competitionGroupMember->competitionGroupFunction()->equals(CompetitionGroupFunction::COACH())) {
                $competitionGroupMembers[] = $competitionGroupMember;
            }
        }

        return self::fromArray($competitionGroupMembers);
    }

    public function getAssistantCoaches(): self
    {
        $competitionGroupMembers = [];
        foreach ($this->competitionGroupMembers as $competitionGroupMember) {
            if ($competitionGroupMember->competitionGroupFunction()
                ->equals(CompetitionGroupFunction::ASSISTANT_COACH())) {
                $competitionGroupMembers[] = $competitionGroupMember;
            }
        }

        return self::fromArray($competitionGroupMembers);
    }

    public function getGymnasts(): self
    {
        $competitionGroupMembers = [];
        foreach ($this->competitionGroupMembers as $competitionGroupMember) {
            if ($competitionGroupMember->competitionGroupFunction()->equals(CompetitionGroupFunction::GYMNAST())) {
                $competitionGroupMembers[] = $competitionGroupMember;
            }
        }

        return self::fromArray($competitionGroupMembers);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->competitionGroupMembers);
    }

    private function __construct()
    {
    }
}
