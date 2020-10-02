<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use App\Shared\Domain\CompetitionGroupRole;
use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class GroupRoleCombinations implements IteratorAggregate
{
    /**
     * @var GroupRoleCombination[]
     */
    private array $groupRoleCombinations;

    /**
     * @param GroupRoleCombination[] $groupRoleCombinations
     *
     * @return static
     */
    public static function fromArray(array $groupRoleCombinations): self
    {
        Assertion::allIsInstanceOf($groupRoleCombinations, GroupRoleCombination::class);
        $self                        = new self();
        $self->groupRoleCombinations = $groupRoleCombinations;

        return $self;
    }

    public function coachGroupFunctionCombinations(): self
    {
        $coachGroupRoleCombinations = [];
        foreach ($this->groupRoleCombinations as $groupFunctionCombination) {
            if (!$groupFunctionCombination->competitionGroupRole()->equals(CompetitionGroupRole::COACH())) {
                continue;
            }
            $coachGroupRoleCombinations[] = $groupFunctionCombination;
        }

        return self::fromArray($coachGroupRoleCombinations);
    }

    public function assistantCoachGroupFunctionCombinations(): self
    {
        $assistantCoachGroupRoleCombinations = [];
        foreach ($this->groupRoleCombinations as $groupFunctionCombination) {
            if (!$groupFunctionCombination->competitionGroupRole()->equals(CompetitionGroupRole::ASSISTANT_COACH())) {
                continue;
            }
            $assistantCoachGroupRoleCombinations[] = $groupFunctionCombination;
        }

        return self::fromArray($assistantCoachGroupRoleCombinations);
    }

    public function gymnastGroupFunctionCombinations(): self
    {
        $gymnastGroupRoleCombinations = [];
        foreach ($this->groupRoleCombinations as $groupFunctionCombination) {
            if (!$groupFunctionCombination->competitionGroupRole()->equals(CompetitionGroupRole::GYMNAST())) {
                continue;
            }
            $gymnastGroupRoleCombinations[] = $groupFunctionCombination;
        }

        return self::fromArray($gymnastGroupRoleCombinations);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->groupRoleCombinations);
    }

    private function __construct()
    {
    }
}
