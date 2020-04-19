<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\DoctrineDbal;

use App\Shared\Domain\Security\PasswordToken;
use App\Shared\Domain\Security\UserCredentials;
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

    public function loadUserByUsername(string $username): UserCredentials
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

    public function findByPasswordToken(PasswordToken $passwordToken, SystemClock $clock): ?UserCredentials
    {
        $statement = $this->connection->createQueryBuilder()
            ->select(
                'uc.*',
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

    public function refreshUser(UserInterface $user): UserCredentials
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    public function insert(UserCredentials $user): void
    {
        $this->connection->createQueryBuilder()
            ->insert('user_credentials')
            ->values(
                [
                    'username'         => ':username',
                    'user_id'          => ':userId',
                    'password_token'   => ':passwordToken',
                    'token_expires_at' => ':tokenExpiresAt'
                ]
            )
            ->set('password_token', ':passwordToken')
            ->set('token_expires_at', ':tokenExpiresAt')
            ->where('username = :username')
            ->setParameters(
                [
                    'username'       => $user->getUsername(),
                    'userId'         => $user->userId(),
                    'passwordToken'  => $user->passwordToken()->toString(),
                    'tokenExpiresAt' => $user->tokenExpiresAt(),
                ],
                ['tokenExpiresAt' => Types::DATETIME_IMMUTABLE]
            )->execute();
    }

    public function update(UserCredentials $user): void
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
        return UserCredentials::class === $class;
    }

    private function hydrate(array $row): UserCredentials
    {
        return UserCredentials::createFromDataSource(
            (int) $row['user_id'],
            $row['role'],
            $row['encrypted_password'],
            $row['username'],
            $row['password_token'] ? PasswordToken::fromString($row['password_token']) : null,
            $row['token_expires_at'] ? new \DateTimeImmutable($row['token_expires_at']) : null
        );
    }
}
