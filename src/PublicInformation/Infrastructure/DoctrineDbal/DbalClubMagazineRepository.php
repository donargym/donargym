<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\ClubMagazine\ClubMagazine;
use App\PublicInformation\Domain\ClubMagazine\ClubMagazines;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalClubMagazineRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(int $id): ?ClubMagazine
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('clubblad')
            ->andWhere('id = :id')
            ->setParameter('id', $id)
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(): ClubMagazines
    {
        $statement     = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('clubblad')
            ->orderBy('datum', 'DESC')
            ->execute();
        $clubMagazines = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $clubMagazines[] = $this->hydrate($row);
        }

        return ClubMagazines::fromArray($clubMagazines);
    }

    /**
     * @return int[]
     */
    public function findAllYears(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('DISTINCT YEAR(datum) as year')
            ->from('clubblad')
            ->orderBy('datum', 'DESC')
            ->execute();
        $years     = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $years[] = (int) $row['year'];
        }

        return $years;
    }

    public function insert(ClubMagazine $clubMagazine): void
    {
        $this->connection->createQueryBuilder()
            ->insert('clubblad')
            ->values(
                [
                    'datum'   => ':issueDate',
                    'locatie' => ':fileName',
                ]
            )
            ->setParameters(
                [
                    'issueDate' => $clubMagazine->issueDate(),
                    'fileName'  => $clubMagazine->fileName(),
                ],
                ['issueDate' => Types::DATETIME_IMMUTABLE]
            )
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('clubblad')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    private function hydrate(array $row): ClubMagazine
    {
        return ClubMagazine::createFromDataSource(
            (int) $row['id'],
            new DateTimeImmutable($row['datum']),
            $row['locatie']
        );
    }
}
