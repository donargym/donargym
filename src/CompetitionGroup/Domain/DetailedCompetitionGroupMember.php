<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use App\Shared\Domain\Category;
use App\Shared\Domain\CompetitionGroupMemberId;
use App\Shared\Domain\CompetitionSeason;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;

final class DetailedCompetitionGroupMember
{
    private CompetitionGroupMemberId  $id;
    private string                    $firstName;
    private string                    $lastName;
    private DateTimeImmutable         $dateOfBirth;
    private ?string                   $pictureFileName;
    private ?string                   $floorMusicFileName;
    private Category                  $category;
    private DetailedTrainingPresences $detailedPresences;

    public static function createFromDataSource(
        CompetitionGroupMemberId $id,
        string $firstName,
        string $lastName,
        DateTimeImmutable $dateOfBirth,
        ?string $pictureFileName,
        ?string $floorMusicFileName,
        DetailedTrainingPresences $detailedTrainingPresences,
        SystemClock $clock
    ): self {
        $self                     = new self();
        $self->id                 = $id;
        $self->firstName          = $firstName;
        $self->lastName           = $lastName;
        $self->dateOfBirth        = $dateOfBirth;
        $self->pictureFileName    = $pictureFileName;
        $self->floorMusicFileName = $floorMusicFileName;
        $self->category           = Category::createFromDateOfBirthForSeason(
            $dateOfBirth,
            CompetitionSeason::getCompetitionSeasonForDate($clock->now())
        );
        $self->detailedPresences  = $detailedTrainingPresences;

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

    public function pictureFileName(): ?string
    {
        return $this->pictureFileName;
    }

    public function floorMusicFileName(): ?string
    {
        return $this->floorMusicFileName;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function detailedPresences(): DetailedTrainingPresences
    {
        return $this->detailedPresences;
    }

    private function __construct()
    {
    }
}
