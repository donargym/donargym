<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Infrastructure\DoctrineDbal;

use App\CompetitionGroup\Domain\BasicCompetitionGroupMember;
use App\CompetitionGroup\Domain\BasicCompetitionGroupMembers;
use App\CompetitionGroup\Domain\CompetitionGroupMember;
use App\CompetitionGroup\Domain\CompetitionGroupMembers;
use App\CompetitionGroup\Domain\DetailedCompetitionGroupMember;
use App\CompetitionGroup\Domain\DetailedTrainingPresences;
use App\CompetitionGroup\Domain\TrainingPresence;
use App\CompetitionGroup\Domain\TrainingPresenceNumbers;
use App\Shared\Domain\CompetitionGroupId;
use App\Shared\Domain\CompetitionGroupMemberId;
use App\Shared\Domain\CompetitionSeason;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalCompetitionGroupMemberRepository
{
    private Connection  $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock      = $clock;
    }

    public function find(
        CompetitionGroupMemberId $competitionGroupMemberId,
        CompetitionGroupId $competitionGroupId,
        int $userId
    ): ?CompetitionGroupMember {
        $statement = $this->connection->createQueryBuilder()
            ->select('cgm.*')
            ->from('competition_group_member', 'cgm')
            ->join('cgm', 'competition_group_account', 'cga', 'cgm.competition_group_account_id = cga.id')
            ->join('cga', 'user', 'u', 'cga.user_id = u.id')
            ->andWhere('u.id = :userId')
            ->andWhere('cgm.id = :competitionGroupMemberId')
            ->setParameters(
                [
                    'userId'                   => $userId,
                    'competitionGroupMemberId' => $competitionGroupMemberId->toString(),
                ]
            )->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }
        $trainingPresenceNumbers = $this->findTotalTrainingPresenceNumbers(
            $competitionGroupMemberId,
            CompetitionSeason::getCompetitionSeasonForDate($this->clock->now()),
            $competitionGroupId
        );

        return $this->hydrate($row, $trainingPresenceNumbers);
    }

    public function findDetailedCompetitionGroupMember(
        CompetitionGroupMemberId $competitionGroupMemberId,
        int $userId
    ): ?DetailedCompetitionGroupMember {
        $statement = $this->connection->createQueryBuilder()
            ->select('cgm.*')
            ->from('competition_group_member', 'cgm')
            ->join('cgm', 'competition_group_account', 'cga', 'cgm.competition_group_account_id = cga.id')
            ->join('cga', 'user', 'u', 'cga.user_id = u.id')
            ->andWhere('u.id = :userId')
            ->andWhere('cgm.id = :competitionGroupMemberId')
            ->setParameters(
                [
                    'userId'                   => $userId,
                    'competitionGroupMemberId' => $competitionGroupMemberId->toString(),
                ]
            )->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrateDetailedCompetitionGroupMember($row);
    }

    public function findForUser(int $userId): BasicCompetitionGroupMembers
    {
        $statement          = $this->connection->createQueryBuilder()
            ->select('cgm.*')
            ->from('competition_group_member', 'cgm')
            ->join('cgm', 'competition_group_account', 'cga', 'cgm.competition_group_account_id = cga.id')
            ->join('cga', 'user', 'u', 'cga.user_id = u.id')
            ->andWhere('u.id = :userId')
            ->setParameters(
                ['userId' => $userId]
            )->execute();
        $competitionMembers = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $competitionMembers[] = $this->hydrateBasicCompetitionGroupMember($row);
        }

        return BasicCompetitionGroupMembers::fromArray($competitionMembers);
    }

    public function findTotalTrainingPresenceNumbers(
        CompetitionGroupMemberId $competitionGroupMemberId,
        CompetitionSeason $competitionSeason,
        CompetitionGroupId $competitionGroupId
    ): TrainingPresenceNumbers {
        $statement                = $this->presenceQuery(
            $competitionGroupMemberId,
            $competitionSeason,
            $competitionGroupId
        )->execute();
        $row                      = $statement->fetch(PDO::FETCH_ASSOC);
        $totalNumberOfTrainings   = (int) $row['count'];
        $statement                = $this->presenceQuery(
            $competitionGroupMemberId,
            $competitionSeason,
            $competitionGroupId
        )
            ->andWhere('cgmtp.presence = :present')
            ->setParameter('present', TrainingPresence::PRESENT)
            ->execute();
        $row                      = $statement->fetch(PDO::FETCH_ASSOC);
        $numberOfTrainingsPresent = (int) $row['count'];

        return TrainingPresenceNumbers::createFromDataSource(
            $totalNumberOfTrainings,
            $numberOfTrainingsPresent
        );
    }

    private function hydrate(array $row, TrainingPresenceNumbers $trainingPresenceNumbers): CompetitionGroupMember
    {
        return CompetitionGroupMember::createFromDataSource(
            CompetitionGroupMemberId::fromString($row['id']),
            $row['first_name'],
            $row['last_name'],
            new DateTimeImmutable($row['date_of_birth']),
            $trainingPresenceNumbers,
            $this->clock
        );
    }

    private function hydrateDetailedCompetitionGroupMember(
        array $row,
        DetailedTrainingPresences $detailedTrainingPresences,
        TrainingPresenceNumbers $totalTrainingPresence
    ): DetailedCompetitionGroupMember {
        return DetailedCompetitionGroupMember::createFromDataSource(
            CompetitionGroupMemberId::fromString($row['id']),
            $row['first_name'],
            $row['last_name'],
            new DateTimeImmutable($row['date_of_birth']),
            $row['picture_file_name'] ?: 'plaatje.jpg',
            $row['floor_music_file_name'] ?: null,
            $detailedTrainingPresences,
            $this->clock
        );
    }

    private function hydrateBasicCompetitionGroupMember(array $row): BasicCompetitionGroupMember
    {
        return BasicCompetitionGroupMember::createFromDataSource(
            CompetitionGroupMemberId::fromString($row['id']),
            $row['first_name'],
            $row['last_name'],
        );
    }

    private function presenceQuery(
        CompetitionGroupMemberId $competitionGroupMemberId,
        CompetitionSeason $competitionSeason,
        CompetitionGroupId $competitionGroupId
    ): QueryBuilder {
        return $this->connection->createQueryBuilder()
            ->select('COUNT("cgmtp.*") AS count')
            ->from('competition_group_member_training_participation', 'cgmtp')
            ->join('cgmtp', 'competition_group_training', 'cgt', 'cgmtp.competition_group_training_id = cgt.id')
            ->join('cgt', 'competition_group_training_time', 'cgtt', 'cgt.competition_group_training_time_id = cgtt.id')
            ->andWhere('cgmtp.competition_group_member_id = :memberId')
            ->andWhere('cgt.training_date >= :seasonStart')
            ->andWhere('cgt.training_date <= :seasonEnd')
            ->andWhere('cgt.training_date <= :seasonEnd')
            ->andWhere('cgtt.competition_group_id = :competitionGroupId')
            ->setParameters(
                [
                    'memberId'           => $competitionGroupMemberId->toString(),
                    'seasonStart'        => $competitionSeason->startDate(),
                    'seasonEnd'          => $competitionSeason->endDate(),
                    'competitionGroupId' => $competitionGroupId->toString()
                ],
                [
                    'seasonStart' => Types::DATETIME_IMMUTABLE,
                    'seasonEnd'   => Types::DATETIME_IMMUTABLE,
                ]
            );
    }
}
