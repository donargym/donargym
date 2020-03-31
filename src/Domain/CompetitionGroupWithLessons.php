<?php

declare(strict_types=1);

namespace App\Domain;

final class CompetitionGroupWithLessons
{
    private int $id;

    private string $name;

    private Lessons $lessons;

    public static function fromDataSource(
        int $id,
        string $name,
        Lessons $lessons
    ): self
    {
        $self          = new self();
        $self->id      = $id;
        $self->name    = $name;
        $self->lessons = $lessons;

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

    public function lessons(): Lessons
    {
        return $this->lessons;
    }

    private function __construct()
    {
    }
}
