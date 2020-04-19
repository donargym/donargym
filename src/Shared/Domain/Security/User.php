<?php
declare(strict_types=1);

namespace App\Shared\Domain\Security;

use App\Shared\Domain\SystemClock;
use DateTimeImmutable;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final class User implements UserInterface
{
    private string                          $role;
    private ?string                         $encryptedPassword;
    private string                          $username;
    private ?PasswordToken                  $passwordToken;
    private ?DateTimeImmutable              $tokenExpiresAt;

    public static function createFromDataSource(
        string $role,
        ?string $encryptedPassword,
        string $username,
        ?PasswordToken $setPasswordToken,
        ?DateTimeImmutable $tokenExpiresAt
    ): self {
        $self                    = new self();
        $self->role              = $role;
        $self->encryptedPassword = $encryptedPassword;
        $self->username          = $username;
        $self->passwordToken     = $setPasswordToken;
        $self->tokenExpiresAt    = $tokenExpiresAt;

        return $self;
    }

    public function getRoles(): array
    {
        return [$this->role];
    }

    public function getPassword(): ?string
    {
        return $this->encryptedPassword;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function eraseCredentials(): void
    {
        return;
    }

    public function passwordToken(): ?PasswordToken
    {
        return $this->passwordToken;
    }

    public function tokenExpiresAt(): ?DateTimeImmutable
    {
        return $this->tokenExpiresAt;
    }

    public function generateSetPasswordToken(SystemClock $clock): void
    {
        $this->passwordToken  = PasswordToken::generate();
        $this->tokenExpiresAt = $clock->now()->modify('+1 day');
    }

    public function setPassword(string $password, UserPasswordEncoderInterface $userPasswordEncoder): void
    {
        $this->encryptedPassword = $userPasswordEncoder->encodePassword($this, $password);
        $this->passwordToken     = null;
        $this->tokenExpiresAt    = null;
    }
}
