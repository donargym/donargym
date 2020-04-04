<?php

declare(strict_types=1);

namespace App\Infrastructure\DoctrineDbal;

use App\Domain\PublicCalendarItem;
use App\Domain\PublicCalendarItems;
use App\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalPublicCalendarItemRepository
{
    private Connection $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    public function find(int $id): ?PublicCalendarItem
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('calendar')
            ->andWhere('id = :id')
            ->setParameter('id', $id)
            ->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function allFutureItems(): PublicCalendarItems
    {
        $today = $this->clock->now();

        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('calendar')
            ->andWhere('datum >= :today')
            ->orderBy('datum', 'ASC')
            ->setParameter('today', $today, Types::DATETIME_IMMUTABLE)
            ->execute();

        $compactCalendarItems = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $compactCalendarItems[] = $this->hydrate($row);
        }

        return PublicCalendarItems::fromArray($compactCalendarItems);
    }

    private function hydrate(array $row): PublicCalendarItem
    {
        return PublicCalendarItem::createFromDataSource(
            (int) $row['id'],
            new DateTimeImmutable($row['datum']),
            $row['activiteit'],
            (string) $row['locatie'],
            (string) $row['tijd']
        );
    }
}
