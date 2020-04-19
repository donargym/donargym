<?php
declare(strict_types=1);

namespace App\Shared\Domain;

use ArrayIterator;
use Assert\Assertion;

final class EmailAddresses implements \IteratorAggregate
{
    /**
     * @var EmailAddress[]
     */
    private array $emailAddresses;

    /**
     * @param EmailAddress[] $emailAddresses
     *
     * @return static
     */
    public static function fromArray(array $emailAddresses): self
    {
        Assertion::allIsInstanceOf($emailAddresses, EmailAddress::class);
        $self                 = new self();
        $self->emailAddresses = $emailAddresses;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->emailAddresses);
    }

    private function __construct()
    {
    }
}
