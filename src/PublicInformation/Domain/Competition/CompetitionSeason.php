<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\Competition;

use DateTimeImmutable;

final class CompetitionSeason
{
    const COMPETITION_START_MONTH = 8;
    const COMPETITION_END_MONTH   = 7;

    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public static function getCompetitionSeasonForDate(DateTimeImmutable $date): self
    {
        $competitionStartYear = (int) $date->format('Y');
        if ((int) $date->format('m') < self::COMPETITION_START_MONTH) {
            $competitionStartYear--;
        }

        $self            = new self();
        $self->startDate = DateTimeImmutable::createFromFormat(
            'Y-m-d H:i:s',
            sprintf('%d-%d-01 00:00:00', $competitionStartYear, self::COMPETITION_START_MONTH)
        );
        $self->endDate   = DateTimeImmutable::createFromFormat(
            'Y-m H:i:s',
            sprintf('%d-%d 23:59:59', $competitionStartYear + 1, self::COMPETITION_END_MONTH)
        )->modify('last day of this month');

        return $self;
    }

    public function dateIsInCompetitionSeason(DateTimeImmutable $date): bool
    {
        return $date >= $this->startDate && $date <= $this->endDate;
    }

    public function equals(CompetitionSeason $other): bool
    {
        return ($other->seasonLabel() === $this->seasonLabel());
    }

    public function startDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function seasonLabel(): string
    {
        return sprintf('%s-%s', $this->startDate->format('Y'), $this->endDate->format('Y'));
    }
}
