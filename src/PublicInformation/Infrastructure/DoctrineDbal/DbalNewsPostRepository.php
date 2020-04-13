<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\News\NewsPost;
use App\PublicInformation\Domain\News\NewsPosts;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalNewsPostRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(int $id): ?NewsPost
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('nieuwsbericht')
            ->andWhere('id = :id')
            ->setParameter('id', $id)
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
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

    public function findNewsPostsForYear(int $year): NewsPosts
    {
        $startDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $year . '-01-01 00:00:00');
        $endDate   = $startDate->modify('+1 year');
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('nieuwsbericht')
            ->andWhere('created_at >= :startDate')
            ->andWhere('created_at < :endDate')
            ->orderBy('created_at', 'ASC')
            ->setParameters(
                ['startDate' => $startDate, 'endDate' => $endDate],
                ['startDate' => Types::DATETIME_IMMUTABLE, 'endDate' => Types::DATETIME_IMMUTABLE]
            )
            ->execute();
        $newsPosts = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $newsPosts[] = $this->hydrate($row);
        }

        return NewsPosts::fromArray($newsPosts);
    }

    /**
     * @return int[]
     */
    public function findYearsForArchive(): array
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('DISTINCT YEAR(created_at) as year')
            ->from('nieuwsbericht')
            ->orderBy('created_at', 'DESC')
            ->execute();
        $years     = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $years[] = (int) $row['year'];
        }

        return $years;
    }

    public function insert(NewsPost $newsPost): void
    {
        $this->connection->createQueryBuilder()
            ->insert('nieuwsbericht')
            ->values(
                [
                    'datumtijd'  => ':dutchDateTime',
                    'jaar'       => ':year',
                    'titel'      => ':title',
                    'bericht'    => ':content',
                    'created_at' => ':createdAt'
                ]
            )
            ->setParameters(
                [
                    'dutchDateTime' => $newsPost->createdAt()->format('d-m-Y: H:i'),
                    'year'          => $newsPost->createdAt()->format('Y'),
                    'title'         => $newsPost->title(),
                    'content'       => $newsPost->content(),
                    'createdAt'     => $newsPost->createdAt(),
                ],
                ['createdAt' => Types::DATETIME_IMMUTABLE]
            )
            ->execute();
    }

    public function update(NewsPost $newsPost): void
    {
        $this->connection->createQueryBuilder()
            ->update('nieuwsbericht')
            ->set('titel', ':title')
            ->set('bericht', ':content')
            ->where('id = :id')
            ->setParameters(
                [
                    'title'   => $newsPost->title(),
                    'content' => $newsPost->content(),
                    'id'      => $newsPost->id(),
                ]
            )
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('nieuwsbericht')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    private function hydrate(array $row): NewsPost
    {
        return NewsPost::createFromDataSource(
            (int) $row['id'],
            $row['titel'],
            $row['bericht'],
            new DateTimeImmutable($row['created_at'])
        );
    }
}
