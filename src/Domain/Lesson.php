<?php

declare(strict_types=1);

namespace App\Domain;

final class Lesson
{
    private int $id;

    private string $day;

    private string $startTime;

    private string $endTime;

    public static function createFromDataSource(
        int $id,
        string $day,
        string $startTime,
        string $endTime
    ): self
    {
        $self = new self();

        $self->id        = $id;
        $self->day       = $day;
        $self->startTime = $startTime;
        $self->endTime   = $endTime;

        return $self;
    }

    public function id(): int
    {
        return $this->id;
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

    private function __construct()
    {
    }
}
