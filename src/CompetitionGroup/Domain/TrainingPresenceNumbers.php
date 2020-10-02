<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

final class TrainingPresenceNumbers
{
    private int   $totalNumberOfTrainings;
    private int   $numberOfTrainingsPresent;
    private int   $totalPresencePercentage;

    public static function createFromDataSource(
        int $totalNumberOfTrainings,
        int $numberOfTrainingsPresent
    ): self {
        $self                           = new self();
        $self->totalNumberOfTrainings   = $totalNumberOfTrainings;
        $self->numberOfTrainingsPresent = $numberOfTrainingsPresent;
        $self->totalPresencePercentage  = (int) round(100 * $numberOfTrainingsPresent / $totalNumberOfTrainings);

        return $self;
    }

    public function totalNumberOfTrainings(): int
    {
        return $this->totalNumberOfTrainings;
    }

    public function numberOfTrainingsPresent(): int
    {
        return $this->numberOfTrainingsPresent;
    }

    public function totalPresencePercentage(): int
    {
        return $this->totalPresencePercentage;
    }

    private function __construct()
    {
    }
}
