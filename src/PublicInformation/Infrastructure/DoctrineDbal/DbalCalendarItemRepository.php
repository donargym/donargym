<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Calendar\CalendarItem;
use App\PublicInformation\Domain\Calendar\CalendarItems;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalCalendarItemRepository
{
    private Connection $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    public function find(int $id): ?CalendarItem
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

    public function allFutureItems(): CalendarItems
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

        return CalendarItems::fromArray($compactCalendarItems);
    }

    private function hydrate(array $row): CalendarItem
    {
        return CalendarItem::createFromDataSource(
            (int) $row['id'],
            new DateTimeImmutable($row['datum']),
            $row['activiteit'],
            (string) $row['locatie'],
            (string) $row['tijd']
        );
    }
}
