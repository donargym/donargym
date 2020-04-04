<?php

declare(strict_types=1);

namespace App\Infrastructure\DoctrineDbal;

use App\Domain\FrequentlyAskedQuestion;
use App\Domain\FrequentlyAskedQuestions;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalFrequentlyAskedQuestionRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): FrequentlyAskedQuestions
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('veelgesteldevragen')
            ->execute();

        $frequentlyAskedQuestions = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $frequentlyAskedQuestions[] = $this->hydrate($row);
        }

        return FrequentlyAskedQuestions::fromArray($frequentlyAskedQuestions);
    }

    private function hydrate(array $row): FrequentlyAskedQuestion
    {
        return FrequentlyAskedQuestion::createFromDataSource(
            (int) $row['id'],
            $row['vraag'],
            $row['antwoord']
        );
    }
}
