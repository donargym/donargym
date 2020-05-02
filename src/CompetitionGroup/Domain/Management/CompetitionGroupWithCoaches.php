<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain\Management;

use App\CompetitionGroup\Domain\CompetitionGroup;

final class CompetitionGroupWithCoaches
{
    private CompetitionGroup        $competitionGroup;
    private CompetitionGroupMembers $coaches;

    public static function createFromDataSource(
        CompetitionGroup $competitionGroup,
        CompetitionGroupMembers $coaches
    ): self {
        $self                   = new self();
        $self->competitionGroup = $competitionGroup;
        $self->coaches         = $coaches;

        return $self;
    }

    public function competitionGroup(): CompetitionGroup
    {
        return $this->competitionGroup;
    }

    public function coaches(): CompetitionGroupMembers
    {
        return $this->coaches;
    }

    private function __construct()
    {
    }
}
