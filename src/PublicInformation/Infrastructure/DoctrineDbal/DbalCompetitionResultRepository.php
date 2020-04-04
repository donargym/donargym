<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\CompetitionResult;
use App\PublicInformation\Domain\Competition\CompetitionResults;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionResultRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAllForGroup(int $groupId): CompetitionResults
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('wedstrijduitslagen')
            ->andWhere('groepen_id = :groupId')
            ->orderBy('datum', 'DESC')
            ->setParameter('groupId', $groupId)
            ->execute();

        $competitionResults = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $competitionResults[] = $this->hydrate($row);
        }

        return CompetitionResults::fromArray($competitionResults);
    }

    private function hydrate(array $row): CompetitionResult
    {
        return CompetitionResult::createFromDataSource(
            $row['naam'],
            $row['locatie'],
            new DateTimeImmutable($row['datum'])
        );
    }
}
