<?php

declare(strict_types=1);

namespace App\Infrastructure\DoctrineDbal;

use App\Domain\SimpleContentPage;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use PDO;

final class DbalSimpleContentPageRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getMostRecentContentForPage(string $pageName): ?SimpleContentPage
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('content')
            ->andWhere('pagina = :page')
            ->orderBy('gewijzigd', 'DESC')
            ->setMaxResults(1)
            ->setParameter('page', $pageName)
            ->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function insert(SimpleContentPage $simpleContentPage): void
    {
        $this->connection->createQueryBuilder()
            ->insert('content')
            ->values(
                [
                    'gewijzigd' => ':changedAt',
                    'pagina'    => ':pageName',
                    'content'   => ':pageContent',
                ]
            )
            ->setParameters(
                [
                    'changedAt'   => $simpleContentPage->changedAt(),
                    'pageName'    => $simpleContentPage->pageName(),
                    'pageContent' => $simpleContentPage->pageContent(),
                ],
                ['changedAt' => Types::DATETIME_IMMUTABLE]
            )
            ->execute();
    }

    private function hydrate(array $row): SimpleContentPage
    {
        return SimpleContentPage::createFromDataSource(
            new DateTimeImmutable($row['gewijzigd']),
            $row['pagina'],
            $row['content']
        );
    }
}
