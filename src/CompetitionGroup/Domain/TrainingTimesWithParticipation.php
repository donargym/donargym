<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use Assert\Assertion;

final class TrainingTimesWithParticipation
{
    /**
     * @var TrainingTimeWithParticipation[]
     */
    private array                     $trainingTimesWithParticipation;
    private TrainingPresenceNumbers   $trainingPresenceNumbers;

    /**
     * @param TrainingTimeWithParticipation[] $trainingTimesWithParticipation
     * @param TrainingPresenceNumbers         $trainingPresenceNumbers
     *
     * @return static
     */
    public static function createFromDataSource(
        array $trainingTimesWithParticipation,
        TrainingPresenceNumbers $trainingPresenceNumbers
    ): self {
        Assertion::allIsInstanceOf($trainingTimesWithParticipation, TrainingTimeWithParticipation::class);
        $self                                 = new self();
        $self->trainingTimesWithParticipation = $trainingTimesWithParticipation;
        $self->trainingPresenceNumbers        = $trainingPresenceNumbers;

        return $self;
    }

    public function trainingTimesWithParticipation(): array
    {
        return $this->trainingTimesWithParticipation;
    }

    public function trainingPresenceNumbers(): TrainingPresenceNumbers
    {
        return $this->trainingPresenceNumbers;
    }

    private function __construct()
    {
    }
}
