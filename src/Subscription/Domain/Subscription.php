<?php
declare(strict_types=1);

namespace App\Subscription\Domain;

use App\Shared\Domain\SystemClock;
use DateTimeImmutable;

final class Subscription
{
    private string             $firstName;
    private string             $lastName;
    private string             $initials;
    private DateTimeImmutable  $dateOfBirth;
    private string             $gender;
    private string             $address;
    private string             $postcode;
    private string             $city;
    private string             $phone1;
    private ?string            $phone2;
    private string             $bankAccountNumber;
    private string             $bankAccountHolder;
    private string             $emailAddress;
    private bool               $haveBeenSubscribed;
    private ?DateTimeImmutable $subscribedFrom;
    private ?DateTimeImmutable $subscribedUntil;
    private bool               $otherClub;
    private ?string            $whatOtherClub;
    private bool               $paidBondContribution;
    private string             $category;
    private array              $days;
    private array              $locations;
    private DateTimeImmutable  $startTime;
    private string             $trainer;
    private ?string            $how;
    private string             $voluntaryWork;
    private bool               $accept;
    private bool               $acceptPrivacyPolicy;
    private bool               $acceptNamePublished;
    private bool               $acceptPicturesPublished;
    private DateTimeImmutable  $subscribedAt;

    public static function createFromForm(array $formData, SystemClock $clock): self
    {
        $self                          = new self();
        $self->firstName               = $formData['firstName'];
        $self->lastName                = $formData['lastName'];
        $self->initials                = $formData['nameLetters'];
        $self->dateOfBirth             = $formData['dateOfBirth'];
        $self->gender                  = $formData['gender'];
        $self->address                 = $formData['address'];
        $self->postcode                = $formData['postcode'];
        $self->city                    = $formData['city'];
        $self->phone1                  = $formData['phone1'];
        $self->phone2                  = $formData['phone2'];
        $self->bankAccountNumber       = $formData['bankAccountNumber'];
        $self->bankAccountHolder       = $formData['bankAccountHolder'];
        $self->emailAddress            = $formData['emailAddress'];
        $self->haveBeenSubscribed      = $formData['haveBeenSubscribed'] === 'Ja';
        $self->subscribedFrom          = $formData['subscribedFrom'];
        $self->subscribedUntil         = $formData['subscribedUntil'];
        $self->otherClub               = $formData['otherClub'] === 'Ja';
        $self->whatOtherClub           = $formData['whatOtherClub'];
        $self->paidBondContribution    = $formData['paidBondContribution'] === 'Ja';
        $self->category                = $formData['category'];
        $self->days                    = $formData['days'];
        $self->locations               = $formData['locations'];
        $self->startTime               = $formData['startTime'];
        $self->trainer                 = $formData['trainer'];
        $self->how                     = $formData['how'];
        $self->voluntaryWork           = $formData['voluntaryWork'];
        $self->accept                  = (bool) $formData['accept'];
        $self->acceptPrivacyPolicy     = (bool) $formData['acceptPrivacyPolicy'];
        $self->acceptNamePublished     = (bool) $formData['acceptNamePublished'];
        $self->acceptPicturesPublished = (bool) $formData['acceptPicturesPublished'];
        $self->subscribedAt            = $clock->now();

        return $self;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function initials(): string
    {
        return $this->initials;
    }

    public function dateOfBirth(): DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function gender(): string
    {
        return $this->gender;
    }

    public function address(): string
    {
        return $this->address;
    }

    public function postcode(): string
    {
        return $this->postcode;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function phone1(): string
    {
        return $this->phone1;
    }

    public function phone2(): ?string
    {
        return $this->phone2;
    }

    public function bankAccountNumber(): string
    {
        return $this->bankAccountNumber;
    }

    public function bankAccountHolder(): string
    {
        return $this->bankAccountHolder;
    }

    public function emailAddress(): string
    {
        return $this->emailAddress;
    }

    public function haveBeenSubscribed(): bool
    {
        return $this->haveBeenSubscribed;
    }

    public function subscribedFrom(): ?DateTimeImmutable
    {
        return $this->subscribedFrom;
    }

    public function subscribedUntil(): ?DateTimeImmutable
    {
        return $this->subscribedUntil;
    }

    public function otherClub(): bool
    {
        return $this->otherClub;
    }

    public function whatOtherClub(): ?string
    {
        return $this->whatOtherClub;
    }

    public function paidBondContribution(): bool
    {
        return $this->paidBondContribution;
    }

    public function category(): string
    {
        return $this->category;
    }

    public function days(): array
    {
        return $this->days;
    }

    public function locations(): array
    {
        return $this->locations;
    }

    public function startTime(): DateTimeImmutable
    {
        return $this->startTime;
    }

    public function trainer(): string
    {
        return $this->trainer;
    }

    public function how(): ?string
    {
        return $this->how;
    }

    public function voluntaryWork(): string
    {
        return $this->voluntaryWork;
    }

    public function accept(): bool
    {
        return $this->accept;
    }

    public function acceptPrivacyPolicy(): bool
    {
        return $this->acceptPrivacyPolicy;
    }

    public function acceptNamePublished(): bool
    {
        return $this->acceptNamePublished;
    }

    public function acceptPicturesPublished(): bool
    {
        return $this->acceptPicturesPublished;
    }

    public function subscribedAt(): DateTimeImmutable
    {
        return $this->subscribedAt;
    }

    private function __construct()
    {
    }
}
