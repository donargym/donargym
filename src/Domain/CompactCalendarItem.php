<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class CompactCalendarItem
{
    private int $id;

    private DateTimeImmutable $date;

    private string $name;

    public static function createFromDataSource(int $id, DateTimeImmutable $date, string $name): self
    {
        $self       = new self();
        $self->id   = $id;
        $self->date = $date;
        $self->name = $name;

        return $self;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function date(): DateTimeImmutable
    {
        return $this->date;
    }

    public function name(): string
    {
        return $this->name;
    }
}
