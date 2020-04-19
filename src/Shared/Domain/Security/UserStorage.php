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

    public function getUser(): ?UserCredentials
    {
        $token = $this->tokenStorage->getToken();
        if (!$token) {
            return null;
        }
        $user = $token->getUser();
        if (!$user instanceof UserCredentials) {
            return null;
        }

        return $user;
    }
}
