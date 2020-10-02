<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class TrainingsWithParticipation implements IteratorAggregate
{
    /**
     * @var TrainingsWithParticipation[]
     */
    private array $trainingWithParticipation;

    /**
     * @param TrainingsWithParticipation[] $trainingWithParticipation
     *
     * @return static
     */
    public static function fromArray(array $trainingWithParticipation): self
    {
        Assertion::allIsInstanceOf($trainingWithParticipation, TrainingsWithParticipation::class);
        $self                            = new self();
        $self->trainingWithParticipation = $trainingWithParticipation;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->trainingWithParticipation);
    }

    private function __construct()
    {
    }
}
