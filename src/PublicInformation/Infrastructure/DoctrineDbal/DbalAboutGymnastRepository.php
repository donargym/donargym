<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Competition\AboutGymnast;
use App\Shared\Domain\SystemClock;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalAboutGymnastRepository
{
    private Connection $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock      = $clock;
    }

    public function findForGymnast(int $gymnastId): ?AboutGymnast
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('s.*')
            ->from('stukje', 's')
            ->join('s', 'persoon', 'g', 's.id = g.stukje_id')
            ->andWhere('g.id = :gymnastId')
            ->setParameter('gymnastId', $gymnastId)
            ->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    private function hydrate(array $row): AboutGymnast
    {
        return AboutGymnast::createFromDataSource(
            (string) $row['toestelleuk'],
            (string) $row['omdattoestelleuk'],
            (string) $row['wedstrijd'],
            (string) $row['element'],
            (string) $row['leren'],
            (string) $row['voorbeeld'],
            (string) $row['overig']
        );
    }
}
