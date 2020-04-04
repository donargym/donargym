<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class CompetitionSeasons implements IteratorAggregate
{
    /**
     * @var CompetitionSeason[]
     */
    private array $competitionSeasons;

    /**
     * @param CompetitionSeason[] $competitionSeasons
     *
     * @return static
     */
    public static function fromArray(array $competitionSeasons): self
    {
        Assertion::allIsInstanceOf($competitionSeasons, CompetitionSeason::class);

        $self                     = new self();
        $self->competitionSeasons = $competitionSeasons;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->competitionSeasons);
    }

    private function __construct()
    {
    }
}
