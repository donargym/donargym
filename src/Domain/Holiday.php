<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class Holiday
{
    private int $id;

    private string $name;

    private DateTimeImmutable $startDate;

    private DateTimeImmutable $endDate;

    public static function createFromDataSource(
        int $id,
        string $name,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): self
    {
        $self            = new self();
        $self->id        = $id;
        $self->name      = $name;
        $self->startDate = $startDate;
        $self->endDate   = $endDate;

        return $self;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function startDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    private function __construct()
    {
    }
}
