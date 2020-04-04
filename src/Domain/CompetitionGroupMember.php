<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class CompetitionGroupMember
{
    private int $id;

    private string $firstName;

    private string $lastName;

    private DateTimeImmutable $dateOfBirth;

    private Category $category;

    private string $pictureFileName;

    private CompetitionGroupFunction $competitionGroupFunction;

    public static function createFromDataSource(
        int $id,
        string $firstName,
        string $lastName,
        DateTimeImmutable $dateOfBirth,
        string $pictureFileName,
        CompetitionGroupFunction $competitionGroupFunction,
        SystemClock $clock
    )
    {
        $category = Category::createFromDateOfBirthForSeason(
            $dateOfBirth,
            CompetitionSeason::getCompetitionSeasonForDate($clock->now())
        );

        $self                           = new self();
        $self->id                       = $id;
        $self->firstName                = $firstName;
        $self->lastName                 = $lastName;
        $self->dateOfBirth              = $dateOfBirth;
        $self->category                 = $category;
        $self->pictureFileName          = $pictureFileName;
        $self->competitionGroupFunction = $competitionGroupFunction;

        return $self;
    }

    public function id(): int
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

    public function competitionGroupFunction(): CompetitionGroupFunction
    {
        return $this->competitionGroupFunction;
    }

    private function __construct()
    {
    }
}
