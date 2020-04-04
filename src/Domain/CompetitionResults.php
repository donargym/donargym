<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class CompetitionResults implements IteratorAggregate
{
    /**
     * @var CompetitionResult[]
     */
    private array $competitionResults;

    /**
     * @param CompetitionResult[] $competitionResults
     *
     * @return static
     */
    public static function fromArray(array $competitionResults): self
    {
        Assertion::allIsInstanceOf($competitionResults, CompetitionResult::class);

        $self                     = new self();
        $self->competitionResults = $competitionResults;

        return $self;
    }

    public function getCompetitionSeasons(): CompetitionSeasons
    {
        $competitionSeasons = [];
        $seasonLabels       = [];
        foreach ($this->competitionResults as $competitionResult) {
            $season = CompetitionSeason::getCompetitionSeasonForDate($competitionResult->competitionDate());
            if (in_array($season->seasonLabel(), $seasonLabels)) {

                continue;
            }

            $competitionSeasons[] = $season;
            $seasonLabels[]       = $season->seasonLabel();
        }

        return CompetitionSeasons::fromArray($competitionSeasons);
    }

    public function competitionResultsForSeason(CompetitionSeason $competitionSeason): self
    {
        $competitionResults = [];
        foreach ($this->competitionResults as $competitionResult) {
            if ($competitionSeason->dateIsInCompetitionSeason($competitionResult->competitionDate())) {
                $competitionResults[] = $competitionResult;
            }
        }

        return self::fromArray($competitionResults);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->competitionResults);
    }

    private function __construct()
    {
    }
}
