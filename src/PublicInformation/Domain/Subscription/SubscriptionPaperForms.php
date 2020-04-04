<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\Subscription;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class SubscriptionPaperForms implements IteratorAggregate
{
    /**
     * @var SubscriptionPaperForm[]
     */
    private array $subscriptionPaperForms;

    /**
     * @param SubscriptionPaperForm[] $subscriptionPaperForms
     *
     * @return static
     */
    public static function fromArray(array $subscriptionPaperForms): self
    {
        Assertion::allIsInstanceOf($subscriptionPaperForms, SubscriptionPaperForm::class);

        $self                         = new self();
        $self->subscriptionPaperForms = $subscriptionPaperForms;

        return $self;
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->subscriptionPaperForms);
    }

    private function __construct()
    {
    }
}
