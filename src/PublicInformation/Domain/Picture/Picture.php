<?php

namespace App\PublicInformation\Domain\Picture;

use App\Shared\Domain\ImageResizer;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Picture
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

    public static function createFromForm(array $formData, string $uploadLocation, ImageResizer $imageResizer): self
    {
        $self       = new self();
        $self->name = $formData['name'];
        /** @var UploadedFile $uploadedFile */
        $uploadedFile   = $formData['file'];
        $self->fileName = sha1(uniqid((string) mt_rand(), true)) . '.' . $uploadedFile->getClientOriginalExtension();
        $imageResizer->resizeByChangingWidth($uploadedFile->getPathname(), $uploadLocation . $self->fileName, 600);

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
