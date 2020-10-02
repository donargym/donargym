<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Infrastructure\DoctrineDbal;

use App\CompetitionGroup\Domain\CompetitionGroup;
use App\CompetitionGroup\Domain\Management\CompetitionGroupMember;
use App\CompetitionGroup\Domain\Management\CompetitionGroupMembers;
use App\CompetitionGroup\Domain\Management\CompetitionGroupsWithCoaches;
use App\CompetitionGroup\Domain\Management\CompetitionGroupWithCoaches;
use App\Shared\Domain\CompetitionGroupRole;
use App\Shared\Domain\CompetitionGroupId;
use App\Shared\Domain\CompetitionGroupMemberId;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupWithTrainersRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): CompetitionGroupsWithCoaches
    {
        $statement         = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('competition_group')
            ->orderBy('sort_order', 'ASC')
            ->execute();
        $competitionGroups = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $coaches             = $this->findCompetitionGroupCoaches($row['id']);
            $competitionGroups[] = $this->hydrate($row, $coaches);
        }

        return CompetitionGroupsWithCoaches::fromArray($competitionGroups);
    }

    private function findCompetitionGroupCoaches(string $groupId): CompetitionGroupMembers
    {
        $statement               = $this->connection->createQueryBuilder()
            ->select('cgm.*')
            ->from('competition_group_member', 'cgm')
            ->join('cgm', 'competition_group_member_role', 'cgmr', 'cgmr.competition_group_member_id = cgm.id')
            ->andWhere('cgmr.competition_group_id = :groupId')
            ->andWhere('cgmr.role = :trainerRole')
            ->orderBy('first_name')
            ->setParameters(
                [
                    'groupId'     => $groupId,
                    'trainerRole' => CompetitionGroupRole::COACH,
                ]
            )
            ->execute();
        $competitionGroupMembers = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $competitionGroupMembers[] = $this->hydrateCompetitionGroupMember($row);
        }

        return CompetitionGroupMembers::fromArray($competitionGroupMembers);
    }

    private function hydrateCompetitionGroupMember(array $row): CompetitionGroupMember
    {
        return CompetitionGroupMember::createFromDataSource(
            CompetitionGroupMemberId::fromString($row['id']),
            $row['first_name'],
            $row['last_name']
        );
    }

    private function hydrate(
        array $competitionGroupRow,
        CompetitionGroupMembers $coaches
    ): CompetitionGroupWithCoaches {
        return CompetitionGroupWithCoaches::createFromDataSource(
            CompetitionGroup::createFromDataSource(
                CompetitionGroupId::fromString($competitionGroupRow['id']),
                $competitionGroupRow['name']
            ),
            $coaches
        );
    }
}
