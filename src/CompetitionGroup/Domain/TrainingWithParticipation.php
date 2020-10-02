<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use DateTimeImmutable;

final class TrainingWithParticipation
{
    private DateTimeImmutable $date;
    private TrainingPresence  $presence;

    public static function createFromDataSource(
        DateTimeImmutable $date,
        TrainingPresence $presence
    ): self {
        $self           = new self();
        $self->date     = $date;
        $self->presence = $presence;

        return $self;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function presence(): TrainingPresence
    {
        return $this->presence;
    }

    private function __construct()
    {
    }
}
