<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\CompactCalendarItem;
use App\Domain\CompactCalendarItems;
use App\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalCompactCalendarItemRepository
{
    private Connection $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    public function allFutureItems(): CompactCalendarItems
    {
        $today = $this->clock->now();

        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'datum', 'activiteit')
            ->from('calendar')
            ->where('datum >= :today')
            ->orderBy('datum', 'ASC')
            ->setParameter('today', $today, Types::DATETIME_IMMUTABLE)
            ->execute();

        $compactCalendarItems = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $compactCalendarItems[] = $this->hydrate($row);
        }

        return CompactCalendarItems::createFromArray($compactCalendarItems);
    }

    private function hydrate(array $row): CompactCalendarItem
    {
        return CompactCalendarItem::createFromDataSource(
            (int) $row['id'],
            new DateTimeImmutable($row['datum']),
            $row['activiteit']
        );
    }
}
