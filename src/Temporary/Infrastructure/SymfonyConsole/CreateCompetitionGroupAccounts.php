<?php
declare(strict_types=1);

namespace App\Temporary\Infrastructure\SymfonyConsole;

use App\Shared\Domain\CompetitionGroup\AccountId;
use App\Shared\Domain\PhoneNumberId;
use App\Shared\Domain\SystemClock;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CreateCompetitionGroupAccounts extends Command
{
    private Connection      $connection;
    private SystemClock     $clock;
    protected static        $defaultName = 'app:create-competition-group-accounts';

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock      = $clock;
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
                    $this->createNewAccount($user);
                    break;
                default:
                    continue;
            }
        }

        return 0;
    }

    private function createNewAccount(array $user): void
    {
        $accountId = AccountId::generate()->toString();
        $this->connection->createQueryBuilder()
            ->insert('competition_group_account')
            ->values(
                [
                    'id'                  => ':id',
                    'user_id'             => ':userId',
                    'street_house_number' => ':streetHouseNumber',
                    'zip_code'            => ':zipCode',
                    'city'                => ':city',
                    'created_at'          => ':createdAt',
                ]
            )->setParameters(
                [
                    'id'                => $accountId,
                    'userId'            => $user['id'],
                    'streetHouseNumber' => $user['straatnr'],
                    'zipCode'           => $user['postcode'],
                    'city'              => $user['plaats'],
                    'createdAt'         => $this->clock->now(),
                ],
                ['createdAt' => Types::DATETIME_IMMUTABLE]
            )->execute();
        if ($user['tel1']) {
            $this->insertPhoneNumber($accountId, $user['tel1']);
        }
        if ($user['tel2']) {
            $this->insertPhoneNumber($accountId, $user['tel2']);
        }
        if ($user['tel3']) {
            $this->insertPhoneNumber($accountId, $user['tel3']);
        }
    }

    private function insertPhoneNumber(string $accountId, string $phoneNumber): void
    {
        $this->connection->createQueryBuilder()
            ->insert('account_phone_number')
            ->values(
                [
                    'id'           => ':id',
                    'account_id'   => ':accountId',
                    'phone_number' => ':phoneNumber',
                ]
            )->setParameters(
                [
                    'id'          => PhoneNumberId::generate()->toString(),
                    'accountId'   => $accountId,
                    'phoneNumber' => $phoneNumber,
                ]
            )->execute();
    }
}
