<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Picture\Picture;
use App\PublicInformation\Domain\Picture\Pictures;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalPictureRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(int $id): ?Picture
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('fotoupload')
            ->andWhere('id = :id')
            ->setParameter('id', $id)
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAllOrderedAlphabetically(): Pictures
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('fotoupload')
            ->orderBy('naam', 'ASC')
            ->execute();
        $pictures  = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $pictures[] = $this->hydrate($row);
        }

        return Pictures::fromArray($pictures);
    }

    public function insert(Picture $picture): void
    {
        $this->connection->createQueryBuilder()
            ->insert('fotoupload')
            ->values(
                [
                    'naam'    => ':name',
                    'locatie' => ':fileName',
                ]
            )
            ->setParameters(
                [
                    'name'     => $picture->name(),
                    'fileName' => $picture->fileName(),
                ]
            )
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('fotoupload')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    private function hydrate(array $row): Picture
    {
        return Picture::createFromDataSource(
            (int) $row['id'],
            $row['naam'],
            (string) $row['locatie']
        );
    }
}
