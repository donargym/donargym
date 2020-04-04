<?php

declare(strict_types=1);

namespace App\Domain;

use ArrayIterator;
use Assert\Assertion;
use IteratorAggregate;

final class ClubMagazines implements IteratorAggregate
{
    /**
     * @var ClubMagazine[]
     */
    private array $clubMagazines;

    /**
     * @param ClubMagazine[] $clubMagazines
     *
     * @return $this
     */
    public static function fromArray(array $clubMagazines): self
    {
        Assertion::allIsInstanceOf($clubMagazines, ClubMagazine::class);

        $self                = new self();
        $self->clubMagazines = $clubMagazines;

        return $self;
    }

    public function clubMagazinesForYear(int $year): self
    {
        $clubMagazines = [];
        foreach ($this->clubMagazines as $clubMagazine) {
            if ((int) $clubMagazine->issueDate()->format('Y') === $year) {
                $clubMagazines[] = $clubMagazine;
            }
        }

        return self::fromArray($clubMagazines);
    }

    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->clubMagazines);
    }

    private function __construct()
    {
    }
}
