<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\CompetitionGroupCompetitionResult;
use App\PublicInformation\Domain\Competition\CompetitionGroupCompetitionResults;
use App\Shared\Domain\CompetitionGroupId;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupCompetitionResultRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAllForGroup(CompetitionGroupId $groupId): CompetitionGroupCompetitionResults
    {
        $statement          = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('competition_group_competition_result')
            ->andWhere('competition_group_id = :groupId')
            ->orderBy('competition_date', 'DESC')
            ->setParameter('groupId', $groupId->toString())
            ->execute();
        $competitionResults = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $competitionResults[] = $this->hydrate($row);
        }

        return CompetitionGroupCompetitionResults::fromArray($competitionResults);
    }

    private function hydrate(array $row): CompetitionGroupCompetitionResult
    {
        return CompetitionGroupCompetitionResult::createFromDataSource(
            $row['name'],
            $row['file_name'],
            new DateTimeImmutable($row['competition_date'])
        );
    }
}
