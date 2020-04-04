<?php

declare(strict_types=1);

namespace App\Domain;

final class SubscriptionPaperForm
{
    private int $id;

    private string $name;

    private string $fileName;

    public static function createFromDataSource(
        int $id,
        string $name,
        string $fileName
    ): self
    {
        $self           = new self();
        $self->id       = $id;
        $self->name     = $name;
        $self->fileName = $fileName;

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

    public function fileName(): string
    {
        return $this->fileName;
    }

    private function __construct()
    {
    }
}
