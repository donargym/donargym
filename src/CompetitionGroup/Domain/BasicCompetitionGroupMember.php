<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use App\Shared\Domain\CompetitionGroupMemberId;

final class BasicCompetitionGroupMember
{
    private CompetitionGroupMemberId $id;
    private string                   $firstName;
    private string                   $lastName;

    public static function createFromDataSource(
        CompetitionGroupMemberId $id,
        string $firstName,
        string $lastName
    ): self {
        $self            = new self();
        $self->id        = $id;
        $self->firstName = $firstName;
        $self->lastName  = $lastName;

        return $self;
    }

    public function id(): CompetitionGroupMemberId
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    private function __construct()
    {
    }
}
