<?php
declare(strict_types=1);

namespace App\Shared\Domain;

final class Address
{
    private string $streetAndHouseNumber;
    private string $zipCode;
    private string $city;

    public static function create(
        string $streetAndHouseNumber,
        string $zipCode,
        string $city
    ): self {
        $self                       = new self();
        $self->streetAndHouseNumber = $streetAndHouseNumber;
        $self->zipCode              = $zipCode;
        $self->city                 = $city;

        return $self;
    }

    public function streetAndHouseNumber(): string
    {
        return $this->streetAndHouseNumber;
    }

    public function zipCode(): string
    {
        return $this->zipCode;
    }

    public function city(): string
    {
        return $this->city;
    }

    private function __construct()
    {
    }
}
