<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\UploadedFile\UploadedFile;
use App\PublicInformation\Domain\UploadedFile\UploadedFiles;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalUploadedFileRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAllOrderedAlphabetically(): UploadedFiles
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('fileupload')
            ->orderBy('naam', 'ASC')
            ->execute();
        $pictures  = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $pictures[] = $this->hydrate($row);
        }

        return UploadedFiles::fromArray($pictures);
    }

    public function remove(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('fileupload')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    private function hydrate(array $row): UploadedFile
    {
        return UploadedFile::createFromDataSource(
            (int) $row['id'],
            $row['naam'],
            (string) $row['locatie']
        );
    }
}
