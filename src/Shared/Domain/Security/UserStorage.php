<?php
declare(strict_types=1);

namespace App\Shared\Domain\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserStorage
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser(): ?User
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }
        $user = $token->getUser();
        if (!$user instanceof User) {
            return null;
        }

        return $user;
    }
}
