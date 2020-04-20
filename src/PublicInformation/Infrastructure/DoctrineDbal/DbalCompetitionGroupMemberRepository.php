<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\CompetitionGroupMember;
use App\PublicInformation\Domain\Competition\CompetitionGroupMembers;
use App\PublicInformation\Domain\Competition\CompetitionGroupRole;
use App\Shared\Domain\CompetitionGroupId;
use App\Shared\Domain\CompetitionGroupMemberId;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalCompetitionGroupMemberRepository
{
    private Connection  $connection;
    private SystemClock $clock;
    private string      $defaultPictureFileName;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection             = $connection;
        $this->clock                  = $clock;
        $this->defaultPictureFileName = 'plaatje.jpg';
    }

    public function find(CompetitionGroupMemberId $id): ?CompetitionGroupMember
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('cgm.*', 'cgmr.role')
            ->from('competition_group_member', 'cgm')
            ->join('cgm', 'competition_group_member_role', 'cgmr', 'cgmr.competition_group_member_id = cgm.id')
            ->andWhere('cgm.id = :id')
            ->setParameter('id', $id->toString())
            ->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAllForGroup(CompetitionGroupId $groupId): CompetitionGroupMembers
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('cgm.*', 'cgmr.role')
            ->from('competition_group_member', 'cgm')
            ->join('cgm', 'competition_group_member_role', 'cgmr', 'cgmr.competition_group_member_id = cgm.id')
            ->orderBy('cgm.date_of_birth', 'ASC')
            ->andWhere('cgmr.competition_group_id = :groupId')
            ->setParameter('groupId', $groupId->toString())
            ->execute();
        $competitionGroupMembers = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $competitionGroupMembers[] = $this->hydrate($row);
        }

        return CompetitionGroupMembers::fromArray($competitionGroupMembers);
    }

    private function hydrate(array $row): CompetitionGroupMember
    {
        return CompetitionGroupMember::createFromDataSource(
            CompetitionGroupMemberId::fromString($row['id']),
            $row['first_name'],
            $row['last_name'],
            new DateTimeImmutable($row['date_of_birth']),
            $row['picture_file_name'] ?: $this->defaultPictureFileName,
            CompetitionGroupRole::fromString($row['role']),
            $this->clock
        );
    }
}
