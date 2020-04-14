<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Holiday\Holiday;
use App\PublicInformation\Domain\Holiday\Holidays;
use App\Shared\Domain\SystemClock;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalHolidayRepository
{
    private Connection  $connection;
    private SystemClock $clock;

    public function __construct(Connection $connection, SystemClock $clock)
    {
        $this->connection = $connection;
        $this->clock      = $clock;
    }

    public function find(int $id): ?Holiday
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('vakanties')
            ->andWhere('id = :id')
            ->setParameter('id', $id)
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findCurrentAndFutureHolidays(): Holidays
    {
        $today     = $this->clock->now();
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('vakanties')
            ->andWhere('tot >= :today')
            ->orderBy('van', 'ASC')
            ->setParameter('today', $today->format('Y-m-d'))
            ->execute();
        $holidays  = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $holidays[] = $this->hydrate($row);
        }

        return Holidays::fromArray($holidays);
    }

    public function insert(Holiday $holiday): void
    {
        $this->connection->createQueryBuilder()
            ->insert('vakanties')
            ->values(
                [
                    'naam' => ':name',
                    'van'  => ':startDate',
                    'tot'  => ':endDate',
                ]
            )
            ->setParameters(
                [
                    'name'      => $holiday->name(),
                    'startDate' => $holiday->startDate(),
                    'endDate'   => $holiday->endDate(),
                ],
                [
                    'startDate' => Types::DATETIME_IMMUTABLE,
                    'endDate'   => Types::DATETIME_IMMUTABLE,
                ]
            )
            ->execute();
    }

    public function update(Holiday $holiday): void
    {
        $this->connection->createQueryBuilder()
            ->update('vakanties')
            ->set('naam', ':name')
            ->set('van', ':startDate')
            ->set('tot', ':endDate')
            ->where('id = :id')
            ->setParameters(
                [
                    'name'      => $holiday->name(),
                    'startDate' => $holiday->startDate(),
                    'endDate'   => $holiday->endDate(),
                    'id'        => $holiday->id(),
                ],
                [
                    'startDate' => Types::DATETIME_IMMUTABLE,
                    'endDate'   => Types::DATETIME_IMMUTABLE,
                ]
            )
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('vakanties')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
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
