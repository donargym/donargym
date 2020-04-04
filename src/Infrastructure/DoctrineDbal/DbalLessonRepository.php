<?php

declare(strict_types=1);

namespace App\Infrastructure\DoctrineDbal;

use App\Domain\Lesson;
use App\Domain\Lessons;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalLessonRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findByGroupId(int $groupId): Lessons
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('id', 'dag', 'tijdvan', 'tijdtot')
            ->from('trainingen')
            ->orderBy('id', 'ASC')
            ->andWhere('groepen_id = :groupId')
            ->setParameter('groupId', $groupId)
            ->execute();

        $lessons = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $lessons[] = $this->hydrate($row);
        }

        return Lessons::fromArray($lessons);
    }

    private function hydrate(array $row): Lesson
    {
        return Lesson::createFromDataSource(
            (int) $row['id'],
            $row['dag'],
            $row['tijdvan'],
            $row['tijdtot']
        );
    }
}
