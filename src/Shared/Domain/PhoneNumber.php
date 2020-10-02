<?php
declare(strict_types=1);

namespace App\Shared\Domain;

final class PhoneNumber
{
    private PhoneNumberId $id;
    private string        $number;

    public static function createFromDataSource(PhoneNumberId $id, string $number): self
    {
        $self              = new self();
        $self->id          = $id;
        $self->number = $number;

        return $self;
    }

    public function id(): PhoneNumberId
    {
        return $this->id;
    }

    public function number(): string
    {
        return $this->number;
    }

    private function __construct()
    {
    }
}
