<?php

namespace App\PublicInformation\Domain\UploadedFile;

use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;

class UploadedFile
{
    private int    $id;
    private string $name;
    private string $fileName;

    public static function createFromDataSource(
        int $id,
        string $name,
        string $fileName
    ): self {
        $self           = new self();
        $self->id       = $id;
        $self->name     = $name;
        $self->fileName = $fileName;

        return $self;
    }

    public static function createFromForm(array $formData, string $uploadLocation): self
    {
        $self       = new self();
        $self->name = $formData['name'];
        /** @var SymfonyUploadedFile $uploadedFile */
        $uploadedFile   = $formData['file'];
        $tempLocation   = $uploadedFile->getPathname();
        $self->fileName = sha1(uniqid(mt_rand(), true)) . '.' . $uploadedFile->getClientOriginalExtension();
        $uploadedFile->move($uploadLocation, $self->fileName);

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
