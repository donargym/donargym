<?php
declare(strict_types=1);

namespace App\Shared\Domain;

final class PhoneNumber
{
    private PhoneNumberId $id;
    private string        $phoneNumber;

    public static function createNew(PhoneNumberId $id, string $phoneNumber): self
    {
        $self              = new self();
        $self->id          = $id;
        $self->phoneNumber = $phoneNumber;

        return $self;
    }

    public function id(): PhoneNumberId
    {
        return $this->id;
    }

    public function phoneNumber(): string
    {
        return $this->phoneNumber;
    }

    private function __construct()
    {
    }
}
