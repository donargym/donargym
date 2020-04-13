<?php

namespace App\PublicInformation\Domain\Picture;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

class Pictures implements IteratorAggregate
{
    /**
     * @var Picture[]
     */
    private array $pictures;

    /**
     * @param Picture[] $pictures
     *
     * @return static
     */
    public static function fromArray(array $pictures): self
    {
        Assertion::allIsInstanceOf($pictures, Picture::class);
        $self           = new self();
        $self->pictures = $pictures;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->pictures);
    }

    private function __construct()
    {
    }
}
