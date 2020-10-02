<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

final class TrainingTimeWithParticipation
{
    private string                                          $day;
    private string                                          $startTime;
    private string                                          $endTime;
    private TrainingsWithParticipation                      $trainingsWithParticipation;
    private TrainingPresenceNumbers                         $trainingPresenceNumbers;

    public static function createFromDataSource(
        string $day,
        string $startTime,
        string $endTime,
        TrainingsWithParticipation $trainingsWithParticipation,
        TrainingPresenceNumbers $trainingPresenceNumbers
    ): self {
        $self                             = new self();
        $self->day                        = $day;
        $self->startTime                  = $startTime;
        $self->endTime                    = $endTime;
        $self->trainingsWithParticipation = $trainingsWithParticipation;
        $self->trainingPresenceNumbers    = $trainingPresenceNumbers;

        return $self;
    }

    public function day(): string
    {
        return $this->day;
    }

    public function startTime(): string
    {
        return $this->startTime;
    }

    public function endTime(): string
    {
        return $this->endTime;
    }

    public function trainingsWithParticipation(): TrainingsWithParticipation
    {
        return $this->trainingsWithParticipation;
    }

    public function trainingPresenceNumbers(): TrainingPresenceNumbers
    {
        return $this->trainingPresenceNumbers;
    }

    private function __construct()
    {
    }
}
