<?php
declare(strict_types=1);

namespace App\Temporary\Infrastructure\SymfonyConsole;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CFixUserDataCommand extends Command
{
    private Connection      $connection;
    protected static $defaultName = 'app:fix-user-data';

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $users = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('user')
            ->execute()
            ->fetchAll();
        foreach ($users as $user) {
            switch ($user['role']) {
                case 'ROLE_TRAINER':
                case 'ROLE_ASSISTENT':
                case 'ROLE_TURNSTER':
                case 'ROLE_COMPETITION_GROUP':
                case 'ROLE_ADMIN':
                $this->insertUser($user['username'], $user['password'], $user['id']);
                if ($user['email2']) {
                    $this->insertUser($user['email2'], $user['password'], $user['id']);
                }
                if ($user['email3']) {
                    $this->insertUser($user['email3'], $user['password'], $user['id']);
                }
                    break;
                default:
                    continue;
            }
        }

        return 0;
    }

    private function insertUser(string $username, string $encryptedPassword, string $userId): void
    {
        if (strtolower($username) === 'admin') {
            $username = 'admin@donargym.nl';
        }
        if (strtolower($username) === 'webmaster@donargym.nl') {
            return;
        }
        try {
            $this->connection->createQueryBuilder()
                ->insert('user_credentials')
                ->values(
                    [
                        'username'           => ':username',
                        'encrypted_password' => ':encryptedPassword',
                        'user_id'            => ':userId',
                    ]
                )
                ->setParameters(
                    [
                        'username'          => $username,
                        'encryptedPassword' => $encryptedPassword,
                        'userId'            => $userId,
                    ]
                )
                ->execute();
        } catch (UniqueConstraintViolationException $exception) {
        }
    }
}
