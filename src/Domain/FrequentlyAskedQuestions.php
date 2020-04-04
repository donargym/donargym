<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class FrequentlyAskedQuestions implements IteratorAggregate
{
    /**
     * @var FrequentlyAskedQuestion[]
     */
    private array $frequentlyAskedQuestions;

    /**
     * @param FrequentlyAskedQuestion[] $frequentlyAskedQuestions
     *
     * @return static
     */
    public static function fromArray(array $frequentlyAskedQuestions): self
    {
        Assertion::allIsInstanceOf($frequentlyAskedQuestions, FrequentlyAskedQuestion::class);

        $self                           = new self();
        $self->frequentlyAskedQuestions = $frequentlyAskedQuestions;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->frequentlyAskedQuestions);
    }

    private function __construct()
    {
    }
}
