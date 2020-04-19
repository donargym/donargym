<?php
declare(strict_types=1);

namespace App\Shared\Domain\Security;

use Assert\Assertion;

final class Role
{
    const ROLE_USER              = 'ROLE_USER';
    const ROLE_COMPETITION_GROUP = 'ROLE_COMPETITION_GROUP';
    const ROLE_ADMIN             = 'ROLE_ADMIN';

    private string $role;

    public static function ROLE_USER(): self
    {
        return self::fromString(self::ROLE_USER);
    }

    public static function ROLE_COMPETITION_GROUP(): self
    {
        return self::fromString(self::ROLE_COMPETITION_GROUP);
    }

    public static function ROLE_ADMIN(): self
    {
        return self::fromString(self::ROLE_ADMIN);
    }

    public static function allAsString(): array
    {
        return [
            self::ROLE_USER,
            self::ROLE_COMPETITION_GROUP,
            self::ROLE_ADMIN,
        ];
    }

    public static function fromString(string $role): self
    {
        Assertion::inArray($role, self::allAsString());
        $self = new self();
        $self->role = $role;
        return $self;
    }

    public function toString(): string
    {
        return $this->role;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    private function __construct()
    {
    }
}
