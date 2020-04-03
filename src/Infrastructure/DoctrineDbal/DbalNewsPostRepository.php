<?php

declare(strict_types=1);

namespace App\Infrastructure\DoctrineDbal;

use App\Domain\NewsPost;
use App\Domain\NewsPosts;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalNewsPostRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findTenMostRecentNewsPosts(): NewsPosts
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('nieuwsbericht')
            ->orderBy('created_at', 'DESC')
            ->setMaxResults(10)
            ->execute();

        $newsPosts = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $newsPosts[] = $this->hydrate($row);
        }

        return NewsPosts::fromArray($newsPosts);
    }

    private function hydrate(array $row): NewsPost
    {
        return NewsPost::createFromDataSource(
            (int) $row['id'],
            $row['titel'],
            $row['bericht'],
            new \DateTimeImmutable($row['created_at'])
        );
    }
}
