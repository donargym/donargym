<?php

declare(strict_types=1);

namespace App\Domain;

use Assert\Assertion;
use Assert\AssertionFailedException;

final class EmailAddress
{
    private string $emailAddress;

    public static function fromString(string $emailAddress): self
    {
        $newEmailAddress               = new self();
        $newEmailAddress->emailAddress = $emailAddress;

        return $newEmailAddress;
    }

    public function equals(EmailAddress $other): bool
    {
        return $other->emailAddress === $this->emailAddress;
    }

    public function isValid(): bool
    {
        try {
            Assertion::email($this->emailAddress);
            return true;
        } catch (AssertionFailedException $exception) {
            return false;
        }
    }

    public function toString(): string
    {
        return $this->emailAddress;
    }

    private function __construct()
    {
    }
}
