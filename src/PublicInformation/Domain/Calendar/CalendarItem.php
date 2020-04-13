<?php
declare(strict_types=1);

namespace App\PublicInformation\Domain\Calendar;

use DateTimeImmutable;

final class CalendarItem
{
    private int               $id;
    private DateTimeImmutable $date;
    private string            $name;
    private ?string            $locationDescription;
    private ?string            $timeDescription;

    public static function createFromDataSource(
        int $id,
        DateTimeImmutable $date,
        string $name,
        ?string $locationDescription,
        ?string $timeDescription
    ): self {
        $self                      = new self();
        $self->id                  = $id;
        $self->date                = $date;
        $self->name                = $name;
        $self->locationDescription = $locationDescription;
        $self->timeDescription     = $timeDescription;

        return $self;
    }

    public static function createFromForm(array $formData): self
    {
        $self                      = new self();
        $self->date                = $formData['date'];
        $self->name                = $formData['activity'];
        $self->locationDescription = $formData['location'];
        $self->timeDescription     = $formData['time'];

        return $self;
    }

    public function updateFromForm(array $formData): void
    {
        $this->date                = $formData['date'];
        $this->name                = $formData['activity'];
        $this->locationDescription = $formData['location'];
        $this->timeDescription     = $formData['time'];
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

    public function locationDescription(): ?string
    {
        return $this->locationDescription;
    }

    public function timeDescription(): ?string
    {
        return $this->timeDescription;
    }

    private function __construct()
    {
    }
}
