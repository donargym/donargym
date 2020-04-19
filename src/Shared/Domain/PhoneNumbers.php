<?php
declare(strict_types=1);

namespace App\Shared\Domain;

use ArrayIterator;
use Assert\Assertion;

final class PhoneNumbers implements \IteratorAggregate
{
    /**
     * @var PhoneNumber[]
     */
    private array $phoneNumbers;

    /**
     * @param PhoneNumber[] $phoneNumbers
     *
     * @return static
     */
    public static function fromArray(array $phoneNumbers): self
    {
        Assertion::allIsInstanceOf($phoneNumbers, PhoneNumber::class);
        $self               = new self();
        $self->phoneNumbers = $phoneNumbers;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->phoneNumbers);
    }

    private function __construct()
    {
    }
}
