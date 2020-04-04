<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\Competition;

use DateTimeImmutable;

final class CompetitionResult
{
    private string $label;

    private string $fileName;

    private DateTimeImmutable $competitionDate;

    public static function createFromDataSource(
        string $label,
        string $fileName,
        DateTimeImmutable $competitionDate
    ): self
    {
        $self                    = new self();
        $self->label             = $label;
        $self->fileName          = $fileName;
        $self->competitionDate   = $competitionDate;

        return $self;
    }

    public function label(): string
    {
        return $this->label;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    public function competitionDate(): DateTimeImmutable
    {
        return $this->competitionDate;
    }

    private function __construct()
    {
    }
}
