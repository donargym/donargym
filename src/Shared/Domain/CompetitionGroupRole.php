<?php
declare(strict_types=1);

namespace App\Shared\Domain;

use Assert\Assertion;

final class CompetitionGroupRole
{
    const COACH           = 'Trainer';
    const ASSISTANT_COACH = 'Assistent-Trainer';
    const GYMNAST         = 'Turnster';
    private string $competitionGroupRole;

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

    public static function fromString(string $competitionGroupRole): self
    {
        Assertion::inArray($competitionGroupRole, self::allAsString());
        $self                       = new self();
        $self->competitionGroupRole = $competitionGroupRole;

        return $self;
    }

    public function equals(CompetitionGroupRole $other): bool
    {
        return $this->toString() === $other->toString();
    }

    public function toString(): string
    {
        return $this->competitionGroupRole;
    }

    public function __toString()
    {
        return $this->toString();
    }

    private function __construct()
    {
    }
}
