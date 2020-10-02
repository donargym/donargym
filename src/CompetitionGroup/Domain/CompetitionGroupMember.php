<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use App\Shared\Domain\Category;
use App\Shared\Domain\CompetitionGroupMemberId;
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
    private TrainingPresenceNumbers  $trainingPresenceNumbers;

    public static function createFromDataSource(
        CompetitionGroupMemberId $id,
        string $firstName,
        string $lastName,
        DateTimeImmutable $dateOfBirth,
        TrainingPresenceNumbers $trainingPresenceNumbers,
        SystemClock $clock
    ): self {
        $self              = new self();
        $self->id          = $id;
        $self->firstName   = $firstName;
        $self->lastName    = $lastName;
        $self->dateOfBirth = $dateOfBirth;
        $self->category    = Category::createFromDateOfBirthForSeason(
            $dateOfBirth,
            CompetitionSeason::getCompetitionSeasonForDate($clock->now())
        );
        $self->trainingPresenceNumbers = $trainingPresenceNumbers;

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

    public function trainingPresenceNumbers(): TrainingPresenceNumbers
    {
        return $this->trainingPresenceNumbers;
    }

    private function __construct()
    {
    }
}
