<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\Competition;

final class CompetitionGroup
{
    private int $id;

    private string $name;

    public static function fromDataSource(
        int $id,
        string $name
    ): self
    {
        $self       = new self();
        $self->id   = $id;
        $self->name = $name;

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

    private function __construct()
    {
    }
}
