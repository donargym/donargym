<?php
declare(strict_types=1);

namespace App\Temporary\Infrastructure\SymfonyConsole;

use App\Shared\Domain\Security\Role;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class FixUserRolesCommand extends Command
{
    private Connection      $connection;
    protected static        $defaultName = 'app:fix-user-roles';

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
                case 'ROLE_COMPETITION_GROUP':
                case 'ROLE_COMPETITION_GROUP':
                case 'ROLE_COMPETITION_GROUP':
                    $this->updateUserWithRole(Role::ROLE_COMPETITION_GROUP, $user['id']);
                    break;
                default:
                    continue;
            }
        }

        return 0;
    }

    private function updateUserWithRole(string $role, string $userId): void
    {
        $this->connection->createQueryBuilder()
            ->update('user')
            ->set('role', ':role')
            ->where('id = :id')
            ->setParameters(
                [
                    'role' => $role,
                    'id'   => $userId,
                ]
            )->execute();
    }
}
