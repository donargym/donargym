<?php

declare(strict_types=1);

namespace App\PublicInformation\Domain\Competition;

use Assert\Assertion;

final class CompetitionGroupFunction
{
    const COACH           = 'Trainer';
    const ASSISTANT_COACH = 'Assistent-Trainer';
    const GYMNAST         = 'Turnster';

    private string $competitionGroupFunction;

    public static function COACH(): self
    {
        return self::fromString(self::COACH);
    }

    public static function ASSISTANT_COACH(): self
    {
        return self::fromString(self::ASSISTANT_COACH);
    }

    public static function GYMNAST(): self
    {
        return self::fromString(self::GYMNAST);
    }

    /**
     * @return string[]
     */
    public static function allAsString(): array
    {
        return [
            self::COACH,
            self::ASSISTANT_COACH,
            self::GYMNAST,
        ];
    }

    public static function fromString(string $competitionGroupFunction): self
    {
        Assertion::inArray($competitionGroupFunction, self::allAsString());

        $self = new self();
        $self->competitionGroupFunction = $competitionGroupFunction;

        return $self;
    }

    public function equals(CompetitionGroupFunction $other): bool
    {
        return $this->toString() === $other->toString();
    }

    public function toString(): string
    {
        return $this->competitionGroupFunction;
    }

    public function __toString()
    {
        return $this->toString();
    }

    private function __construct()
    {
    }
}
