<?php
declare(strict_types=1);

namespace App\Shared\Infrastructure\DoctrineDbal;

use App\Shared\Domain\Security\User;
use Doctrine\DBAL\Connection;
use PDO;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class DbalUserRepository implements UserProviderInterface
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
                'uc.username',
                'uc.encrypted_password',
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

    public function refreshUser(UserInterface $user): User
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        return $this->loadUserByUsername($user->getUsername());
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
            $row['username']
        );
    }
}
