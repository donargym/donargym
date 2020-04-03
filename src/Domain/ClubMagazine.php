<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class ClubMagazine
{
    private int $id;

    private DateTimeImmutable $issueDate;

    private string $fileName;

    public static function createFromDataSource(
        int $id,
        DateTimeImmutable $issueDate,
        string $fileName
    ): self
    {
        $self = new self();

        $self->id        = $id;
        $self->issueDate = $issueDate;
        $self->fileName  = $fileName;

        return $self;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function issueDate(): DateTimeImmutable
    {
        return $this->issueDate;
    }

    public function fileName(): string
    {
        return $this->fileName;
    }

    private function __construct()
    {
    }
}
