<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class SimpleContentPage
{
    private DateTimeImmutable $changedAt;

    private string $pageName;

    private string $pageContent;

    public static function createNew(
        string $pageName,
        string $pageContent,
        SystemClock $clock
    ): self
    {
        $self              = new self();
        $self->changedAt   = $clock->now();
        $self->pageName    = $pageName;
        $self->pageContent = $pageContent;

        return $self;
    }

    public static function createFromDataSource(
        DateTimeImmutable $changedAt,
        string $pageName,
        string $pageContent
    ): self
    {
        $self              = new self();
        $self->changedAt   = $changedAt;
        $self->pageName    = $pageName;
        $self->pageContent = $pageContent;

        return $self;
    }

    public function changedAt(): DateTimeImmutable
    {
        return $this->changedAt;
    }

    public function pageName(): string
    {
        return $this->pageName;
    }

    public function pageContent(): string
    {
        return $this->pageContent;
    }
}
