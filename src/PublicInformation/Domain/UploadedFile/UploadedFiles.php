<?php

namespace App\PublicInformation\Domain\UploadedFile;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

class UploadedFiles implements IteratorAggregate
{
    /**
     * @var UploadedFile[]
     */
    private array $pictures;

    /**
     * @param UploadedFile[] $pictures
     *
     * @return static
     */
    public static function fromArray(array $pictures): self
    {
        Assertion::allIsInstanceOf($pictures, UploadedFile::class);
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
