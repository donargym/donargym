<?php
declare(strict_types=1);

namespace App\PublicInformation\Domain\ClubMagazine;

use DateTimeImmutable;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

final class ClubMagazine
{
    private int $id;
    private DateTimeImmutable $issueDate;
    private string $fileName;

    public static function createFromDataSource(
        int $id,
        DateTimeImmutable $issueDate,
        string $fileName
    ): self {
        $self = new self();
        $self->id        = $id;
        $self->issueDate = $issueDate;
        $self->fileName  = $fileName;

        return $self;
    }

    public static function createFromForm(array $formData, string $uploadLocation): self
    {
        $self            = new self();
        $self->issueDate = $formData['issueDate'];
        /** @var SymfonyUploadedFile $uploadedFile */
        $uploadedFile   = $formData['file'];
        $self->fileName = sha1(uniqid((string) mt_rand(), true)) . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move($uploadLocation, $self->fileName);

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
