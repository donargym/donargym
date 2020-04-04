<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\FrequentlyAskedQuestion;

final class FrequentlyAskedQuestion
{
    private int $id;
    private string $question;
    private string $answer;

    public static function createFromDataSource(
        int $id,
        string $question,
        string $answer
    ): self
    {
        $self = new self();
        $self->id = $id;
        $self->question = $question;
        $self->answer = $answer;

        return $self;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function question(): string
    {
        return $this->question;
    }

    public function answer(): string
    {
        return $this->answer;
    }

    private function __construct()
    {
    }
}
