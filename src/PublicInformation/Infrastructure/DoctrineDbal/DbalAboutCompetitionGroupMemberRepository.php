<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\AboutCompetitionGroupMember;
use App\Shared\Domain\CompetitionGroupMemberId;
use App\Shared\Domain\SystemClock;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalAboutCompetitionGroupMemberRepository
{
    private Connection  $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock      = $clock;
    }

    public function findForCompetitionGroupMember(CompetitionGroupMemberId $competitionGroupMemberId): ?AboutCompetitionGroupMember
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('acgm.*')
            ->from('about_competition_group_member', 'acgm')
            ->join('acgm', 'competition_group_member', 'cgm', 'acgm.competition_group_member_id = cgm.id')
            ->andWhere('cgm.id = :competitionGroupMemberId')
            ->setParameter('competitionGroupMemberId', $competitionGroupMemberId->toString())
            ->execute();
        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    private function hydrate(array $row): AboutCompetitionGroupMember
    {
        return AboutCompetitionGroupMember::createFromDataSource(
            (string) $row['most_fun_apparatus'],
            (string) $row['explanation_about_most_fun_apparatus'],
            (string) $row['most_fun_competition'],
            (string) $row['most_fun_or_hardest_skill'],
            (string) $row['would_like_to_learn'],
            (string) $row['example_gymnast'],
            (string) $row['anything_else']
        );
    }
}
