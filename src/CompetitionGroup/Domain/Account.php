<?php
declare(strict_types=1);

namespace App\Shared\Domain\CompetitionGroup;

use App\Shared\Domain\Address;
use App\Shared\Domain\EmailAddresses;
use App\Shared\Domain\PhoneNumbers;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;

final class Account
{
    private AccountId         $accountId;
    private Address           $address;
    private PhoneNumbers      $phoneNumbers;
    private EmailAddresses    $emailAddresses;
    private DateTimeImmutable $createdAt;

    public static function createNew(
        Address $address,
        PhoneNumbers $phoneNumbers,
        EmailAddresses $emailAddresses,
        SystemClock $clock
    ): self {
        $self                 = new self();
        $self->accountId      = AccountId::generate();
        $self->address        = $address;
        $self->phoneNumbers   = $phoneNumbers;
        $self->emailAddresses = $emailAddresses;
        $self->createdAt      = $clock->now();

        return $self;
    }

    public static function createFromDataSource(
        AccountId $accountId,
        Address $address,
        PhoneNumbers $phoneNumbers,
        EmailAddresses $emailAddresses,
        DateTimeImmutable $createdAt
    ): self {
        $self                 = new self();
        $self->accountId      = $accountId;
        $self->address        = $address;
        $self->phoneNumbers   = $phoneNumbers;
        $self->emailAddresses = $emailAddresses;
        $self->createdAt      = $createdAt;

        return $self;
    }

    public function accountId(): AccountId
    {
        return $this->accountId;
    }

    public function address(): Address
    {
        return $this->address;
    }

    public function phoneNumbers(): PhoneNumbers
    {
        return $this->phoneNumbers;
    }

    public function emailAddresses(): EmailAddresses
    {
        return $this->emailAddresses;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    private function __construct()
    {
    }
}
