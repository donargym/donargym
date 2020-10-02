<?php
declare(strict_types=1);

namespace App\PublicInformation\Domain\Competition;

use App\Shared\Domain\Category;
use App\Shared\Domain\CompetitionGroupMemberId;
use App\Shared\Domain\CompetitionGroupRole;
use App\Shared\Domain\CompetitionSeason;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;

final class CompetitionGroupMember
{
    private CompetitionGroupMemberId $id;
    private string                   $firstName;
    private string                   $lastName;
    private DateTimeImmutable        $dateOfBirth;
    private Category                 $category;
    private string                   $pictureFileName;
    private CompetitionGroupRole     $competitionGroupRole;

    public static function createFromDataSource(
        CompetitionGroupMemberId $id,
        string $firstName,
        string $lastName,
        DateTimeImmutable $dateOfBirth,
        string $pictureFileName,
        CompetitionGroupRole $competitionGroupRole,
        SystemClock $clock
    ) {
        $category                   = Category::createFromDateOfBirthForSeason(
            $dateOfBirth,
            CompetitionSeason::getCompetitionSeasonForDate($clock->now())
        );
        $self                       = new self();
        $self->id                   = $id;
        $self->firstName            = $firstName;
        $self->lastName             = $lastName;
        $self->dateOfBirth          = $dateOfBirth;
        $self->category             = $category;
        $self->pictureFileName      = $pictureFileName;
        $self->competitionGroupRole = $competitionGroupRole;

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

    public function dateOfBirth(): DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function pictureFileName(): string
    {
        return $this->pictureFileName;
    }

    public function competitionGroupRole(): CompetitionGroupRole
    {
        return $this->competitionGroupRole;
    }

    private function __construct()
    {
    }
}
