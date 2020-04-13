<?php

namespace App\PublicInformation\Domain\UploadedFile;

class UploadedFile
{
    private int    $id;
    private string $name;
    private string $fileName;

    public static function createFromDataSource(
        $id,
        $name,
        $fileName
    ): self {
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
