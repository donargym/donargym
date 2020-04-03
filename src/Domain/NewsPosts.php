<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class NewsPosts implements IteratorAggregate
{
    /**
     * @var NewsPost[]
     */
    private array $newsPosts;

    /**
     * @param NewsPost[] $newsPosts
     *
     * @return static
     */
    public static function fromArray(array $newsPosts): self
    {
        Assertion::allIsInstanceOf($newsPosts, NewsPost::class);

        $self            = new self();
        $self->newsPosts = $newsPosts;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->newsPosts);
    }
}
