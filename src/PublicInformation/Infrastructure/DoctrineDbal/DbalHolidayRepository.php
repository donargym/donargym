<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Holiday\Holiday;
use App\PublicInformation\Domain\Holiday\Holidays;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalHolidayRepository
{
    private Connection $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock      = $clock;
    }

    public function findCurrentAndFutureHolidays(): Holidays
    {
        $today = $this->clock->now();

        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('vakanties')
            ->andWhere('tot >= :today')
            ->orderBy('van', 'ASC')
            ->setParameter('today', $today->format('Y-m-d'))
            ->execute();

        $holidays = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $holidays[] = $this->hydrate($row);
        }

        return Holidays::fromArray($holidays);
    }

    private function hydrate(array $row): Holiday
    {
        return Holiday::createFromDataSource(
            (int) $row['id'],
            $row['naam'],
            new DateTimeImmutable($row['van']),
            new DateTimeImmutable($row['tot'])
        );
    }
}
