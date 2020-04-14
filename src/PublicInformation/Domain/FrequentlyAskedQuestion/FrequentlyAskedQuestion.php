<?php
declare(strict_types=1);

namespace App\PublicInformation\Domain\FrequentlyAskedQuestion;

final class FrequentlyAskedQuestion
{
    private int    $id;
    private string $question;
    private string $answer;

    public static function createFromDataSource(
        int $id,
        string $question,
        string $answer
    ): self {
        $self           = new self();
        $self->id       = $id;
        $self->question = $question;
        $self->answer   = $answer;

        return $self;
    }

    public static function createFromForm(array $formData): self
    {
        $self           = new self();
        $self->question = $formData['question'];
        $self->answer   = $formData['answer'];

        return $self;
    }

    public function updateFromForm(array $formData): void
    {
        $this->question = $formData['question'];
        $this->answer   = $formData['answer'];
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
