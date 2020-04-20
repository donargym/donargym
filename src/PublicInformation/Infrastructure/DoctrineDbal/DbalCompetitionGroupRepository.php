<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\CompetitionGroup;
use App\PublicInformation\Domain\Competition\CompetitionGroups;
use App\Shared\Domain\CompetitionGroupId;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(CompetitionGroupId $groupId): ?CompetitionGroup
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('competition_group')
            ->andWhere('id = :id')
            ->setParameter('id', $groupId->toString())
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function allGroups(): CompetitionGroups
    {
        $statement         = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('competition_group')
            ->orderBy('sort_order', 'ASC')
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
            CompetitionGroupId::fromString($row['id']),
            $row['name']
        );
    }
}
