<?php
declare(strict_types=1);

namespace App\PublicInformation\Domain\Holiday;

use DateTimeImmutable;

final class Holiday
{
    private int               $id;
    private string            $name;
    private DateTimeImmutable $startDate;
    private DateTimeImmutable $endDate;

    public static function createFromDataSource(
        int $id,
        string $name,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): self {
        $self            = new self();
        $self->id        = $id;
        $self->name      = $name;
        $self->startDate = $startDate;
        $self->endDate   = $endDate;

        return $self;
    }

    public static function createFromForm(array $formData): self
    {
        $self            = new self();
        $self->name      = $formData['name'];
        $self->startDate = $formData['startDate'];
        $self->endDate   = $formData['endDate'];

        return $self;
    }

    public function updateFromForm(array $formData): void
    {
        $this->name      = $formData['name'];
        $this->startDate = $formData['startDate'];
        $this->endDate   = $formData['endDate'];
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
