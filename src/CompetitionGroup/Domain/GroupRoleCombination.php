<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use App\Shared\Domain\CompetitionGroupRole;

final class GroupRoleCombination
{
    private CompetitionGroup     $competitionGroup;
    private CompetitionGroupRole $competitionGroupRole;

    public static function createFromDataSource(
        CompetitionGroup $competitionGroup,
        CompetitionGroupRole $competitionGroupRole
    ): self {
        $self                       = new self();
        $self->competitionGroup     = $competitionGroup;
        $self->competitionGroupRole = $competitionGroupRole;

        return $self;
    }

    public function competitionGroup(): CompetitionGroup
    {
        return $this->competitionGroup;
    }

    public function competitionGroupRole(): CompetitionGroupRole
    {
        return $this->competitionGroupRole;
    }

    private function __construct()
    {
    }
}
