<?php
declare(strict_types=1);

namespace App\Temporary\Infrastructure\SymfonyConsole;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixUserDataCommand extends Command
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
            $this->insertUser($user['username'], $user['password'], $user['id']);
            if ($user['email2']) {
                $this->insertUser($user['email2'], $user['password'], $user['id']);
            }
            if ($user['email3']) {
                $this->insertUser($user['email3'], $user['password'], $user['id']);
            }
        }

        return 0;
    }

    private function insertUser(string $username, string $encryptedPassword, string $userId): void
    {
        if (strtolower($username) === 'admin') {
            $username = 'admin@donargym.nl';
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
