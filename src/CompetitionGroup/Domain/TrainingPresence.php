<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Domain;

use Assert\Assertion;

final class TrainingPresence
{
    const ABSENT_WITHOUT_NOTICE = 'absent without notice';
    const ABSENT_WITH_NOTICE    = 'absent with notice';
    const PRESENT               = 'present';
    private string $presence;

    public static function allAsString(): array
    {
        return [
            self::ABSENT_WITHOUT_NOTICE,
            self::ABSENT_WITH_NOTICE,
            self::PRESENT,
        ];
    }

    public static function fromString(string $presence): self
    {
        Assertion::inArray($presence, self::allAsString());
        $self           = new self();
        $self->presence = $presence;

        return $self;
    }

    public function toString(): string
    {
        return $this->presence;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    private function __construct()
    {
    }
}
