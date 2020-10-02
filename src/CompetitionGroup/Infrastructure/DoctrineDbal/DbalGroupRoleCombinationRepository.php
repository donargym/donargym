<?php
declare(strict_types=1);

namespace App\CompetitionGroup\Infrastructure\DoctrineDbal;

use App\CompetitionGroup\Domain\GroupRoleCombinations;
use App\Shared\Domain\CompetitionGroupMemberId;
use Doctrine\DBAL\Connection;

final class DbalGroupRoleCombinationRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAllByAccountIdAndMemberId(
        CompetitionGroupMemberId $competitionGroupMemberId,
        int $userId
    ): GroupRoleCombinations {

    }
}
