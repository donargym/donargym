<?php
declare(strict_types=1);

namespace App\PublicInformation\Domain\Subscription;

use Symfony\Component\HttpFoundation\File\UploadedFile;

final class SubscriptionPaperForm
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
        /** @var UploadedFile $uploadedFile */
        $uploadedFile   = $formData['file'];
        $self->fileName = sha1(uniqid((string) mt_rand(), true)) . '.' . $uploadedFile->getClientOriginalExtension();
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
