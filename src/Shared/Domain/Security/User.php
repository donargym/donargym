<?php
declare(strict_types=1);

namespace App\Shared\Domain\Security;

use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface
{
    private string $role;
    private string $encryptedPassword;
    private string $username;

    public static function createFromDataSource(
        string $role,
        string $encryptedPassword,
        string $username
    ): self {
        $self                    = new self();
        $self->role              = $role;
        $self->encryptedPassword = $encryptedPassword;
        $self->username          = $username;

        return $self;
    }

    public function getRoles()
    {
        return [$this->role];
    }

    public function getPassword()
    {
        return $this->encryptedPassword;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        return;
    }
}
