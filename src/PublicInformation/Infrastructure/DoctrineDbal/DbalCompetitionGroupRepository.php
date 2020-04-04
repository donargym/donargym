<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\CompetitionGroup;
use App\PublicInformation\Domain\Competition\CompetitionGroups;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(int $groupId): ?CompetitionGroup
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('groepen')
            ->andWhere('id = :id')
            ->setParameter('id', $groupId)
            ->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function allGroups(): CompetitionGroups
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'name')
            ->from('groepen')
            ->orderBy('id', 'ASC')
            ->execute();

        $competitionGroups = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $competitionGroups[] = $this->hydrate($row);
        }

        return CompetitionGroups::fromArray($competitionGroups);
    }

    private function hydrate(array $row): CompetitionGroup
    {
        return CompetitionGroup::fromDataSource(
            (int) $row['id'],
            $row['name']
        );
    }
}
