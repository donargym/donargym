<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\News;

use DateTimeImmutable;

final class NewsPost
{
    private int $id;

    private string $title;

    private string $content;

    private DateTimeImmutable $createdAt;

    public static function createFromDataSource(
        int $id,
        string $title,
        string $content,
        DateTimeImmutable $createdAt
    ): self
    {
        $self            = new self();
        $self->id        = $id;
        $self->title     = $title;
        $self->content   = $content;
        $self->createdAt = $createdAt;

        return $self;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function __construct()
    {
    }
}
