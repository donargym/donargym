<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use Assert\Assertion;
use DateTimeImmutable;

final class Category
{
    const MINI       = 'Mini';
    const VOORINSTAP = 'Voorinstap';
    const INSTAP     = 'Instap';
    const PUPIL1     = 'Pupil 1';
    const PUPIL2     = 'Pupil 2';
    const JEUGD1     = 'Jeugd 1';
    const JEUGD2     = 'Jeugd 2';
    const JUNIOR     = 'Junior';
    const SENIOR     = 'Senior';

    private string $category;

    public static function MINI(): self
    {
        return self::fromString(self::MINI);
    }

    public static function VOORINSTAP(): self
    {
        return self::fromString(self::VOORINSTAP);
    }

    public static function INSTAP(): self
    {
        return self::fromString(self::INSTAP);
    }

    public static function PUPIL1(): self
    {
        return self::fromString(self::PUPIL1);
    }

    public static function PUPIL2(): self
    {
        return self::fromString(self::PUPIL2);
    }

    public static function JEUGD1(): self
    {
        return self::fromString(self::JEUGD1);
    }

    public static function JEUGD2(): self
    {
        return self::fromString(self::JEUGD2);
    }

    public static function JUNIOR(): self
    {
        return self::fromString(self::JUNIOR);
    }

    public static function SENIOR(): self
    {
        return self::fromString(self::SENIOR);
    }

    /**
     * @return string[]
     */
    public static function allAsString(): array
    {
        return [
            self::MINI,
            self::VOORINSTAP,
            self::INSTAP,
            self::PUPIL1,
            self::PUPIL2,
            self::JEUGD1,
            self::JEUGD2,
            self::JUNIOR,
            self::SENIOR,
        ];
    }

    public static function createFromDateOfBirthForSeason(DateTimeImmutable $dateOfBirth, CompetitionSeason $competitionSeason): self
    {
        switch ((int) $competitionSeason->startDate()->format('Y') - (int) $dateOfBirth->format('Y')) {
            case 0:
            case 1:
            case 2:
            case 3:
            case 4:
            case 5:
            case 6:
                return self::fromString(self::MINI);
            case 7:
                return self::fromString(self::VOORINSTAP);
            case 8:
                return self::fromString(self::INSTAP);
            case 9:
                return self::fromString(self::PUPIL1);
            case 10:
                return self::fromString(self::PUPIL2);
            case 11:
                return self::fromString(self::JEUGD1);
            case 12:
                return self::fromString(self::JEUGD2);
            case 13:
            case 14:
                return self::fromString(self::JUNIOR);
            default:
                return self::fromString(self::SENIOR);
        }
    }

    public static function fromString(string $category): self
    {
        Assertion::inArray($category, self::allAsString());

        $self           = new self();
        $self->category = $category;

        return $self;
    }

    public function toString(): string
    {
        return $this->category;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    private function __construct()
    {
    }
}
