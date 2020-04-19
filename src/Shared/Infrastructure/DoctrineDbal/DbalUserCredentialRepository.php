<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\DoctrineDbal;

use App\Shared\Domain\Security\PasswordToken;
use App\Shared\Domain\Security\User;
use App\Shared\Domain\SystemClock;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class DbalUserCredentialRepository implements UserProviderInterface
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function loadUserByUsername(string $username): User
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'uc.*',
                'u.id',
                'u.role'
            )->from('user_credentials', 'uc')
            ->join('uc', 'user', 'u', 'u.id = uc.user_id')
            ->andWhere('uc.username = :username')
            ->setParameter('username', $username)
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new UsernameNotFoundException();
        }

        return $this->hydrate($row);
    }

    public function findByPasswordToken(PasswordToken $passwordToken, SystemClock $clock): ?User
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'uc.*',
                'u.id',
                'u.role'
            )->from('user_credentials', 'uc')
            ->join('uc', 'user', 'u', 'u.id = uc.user_id')
            ->andWhere('uc.password_token = :passwordToken')
            ->andWhere('uc.token_expires_at > :now')
            ->setParameters(
                [
                    'passwordToken' => $passwordToken->toString(),
                    'now'           => $clock->now()
                ],
                ['now' => Types::DATETIME_IMMUTABLE]
            )
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            throw new UsernameNotFoundException();
        }

        return $this->hydrate($row);
    }

    public function refreshUser(UserInterface $user): User
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function update(User $user): void
    {
        $this->connection->createQueryBuilder()
            ->update('user_credentials')
            ->set('encrypted_password', ':password')
            ->set('password_token', ':passwordToken')
            ->set('token_expires_at', ':tokenExpiresAt')
            ->where('username = :username')
            ->setParameters(
                [
                    'password'       => $user->getPassword(),
                    'passwordToken'  => $user->passwordToken() ? $user->passwordToken()->toString() : null,
                    'tokenExpiresAt' => $user->tokenExpiresAt() ? $user->tokenExpiresAt()->format('Y-m-d H:i:s') : null,
                    'username'       => $user->getUsername(),
                ]
            )->execute();
    }

    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }

    private function hydrate(array $row): User
    {
        return User::createFromDataSource(
            $row['role'],
            $row['encrypted_password'],
            $row['username'],
            $row['password_token'] ? PasswordToken::fromString($row['password_token']) : null,
            $row['token_expires_at'] ? new \DateTimeImmutable($row['token_expires_at']) : null
        );
    }
}
