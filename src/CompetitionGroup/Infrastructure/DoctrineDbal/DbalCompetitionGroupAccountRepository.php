<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Infrastructure\DoctrineDbal;

use App\CompetitionGroup\Domain\CompetitionGroupAccount;
use App\CompetitionGroup\Domain\CompetitionGroupAccountId;
use App\Shared\Domain\Address;
use App\Shared\Domain\EmailAddress;
use App\Shared\Domain\EmailAddresses;
use App\Shared\Domain\PhoneNumber;
use App\Shared\Domain\PhoneNumberId;
use App\Shared\Domain\PhoneNumbers;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupAccountRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findForUser(int $userId): ?CompetitionGroupAccount
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('competition_group_account')
            ->andWhere('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate(
            $row,
            $this->findPhoneNumbers(CompetitionGroupAccountId::fromString($row['id'])),
            $this->findEmailAdresses($userId)
        );
    }

    private function findEmailAdresses(int $userId): EmailAddresses
    {
        $statement      = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('user_credentials')
            ->andWhere('user_id = :userId')
            ->setParameters(
                [
                    'userId' => $userId,
                ]
            )->execute();
        $emailAddresses = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $emailAddresses[] = EmailAddress::fromString($row['username']);
        }

        return EmailAddresses::fromArray($emailAddresses);
    }

    private function findPhoneNumbers(CompetitionGroupAccountId $competitionGroupAccountId): PhoneNumbers
    {
        $statement    = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('account_phone_number')
            ->andWhere('account_id = :competitionGroupAccountId')
            ->setParameters(
                [
                    'competitionGroupAccountId' => $competitionGroupAccountId->toString(),
                ]
            )->execute();
        $phoneNumbers = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $phoneNumbers[] = PhoneNumber::createFromDataSource(
                PhoneNumberId::fromString($row['id']),
                $row['phone_number']
            );
        }

        return PhoneNumbers::fromArray($phoneNumbers);
    }

    private function hydrate(
        array $row,
        PhoneNumbers $phoneNumbers,
        EmailAddresses $emailAddresses
    ): CompetitionGroupAccount {
        return CompetitionGroupAccount::createFromDataSource(
            CompetitionGroupAccountId::fromString($row['id']),
            Address::create(
                $row['street_house_number'],
                $row['zip_code'],
                $row['city']
            ),
            $phoneNumbers,
            $emailAddresses,
            new \DateTimeImmutable($row['created_at'])
        );
    }
}
