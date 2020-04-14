<?php
declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\FrequentlyAskedQuestion\FrequentlyAskedQuestion;
use App\PublicInformation\Domain\FrequentlyAskedQuestion\FrequentlyAskedQuestions;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalFrequentlyAskedQuestionRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(int $id): ?FrequentlyAskedQuestion
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('veelgesteldevragen')
            ->andWhere('id = :id')
            ->setParameter('id', $id)
            ->execute();
        $row       = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(): FrequentlyAskedQuestions
    {
        $statement                = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('veelgesteldevragen')
            ->execute();
        $frequentlyAskedQuestions = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $frequentlyAskedQuestions[] = $this->hydrate($row);
        }

        return FrequentlyAskedQuestions::fromArray($frequentlyAskedQuestions);
    }

    public function insert(FrequentlyAskedQuestion $frequentlyAskedQuestion): void
    {
        $this->connection->createQueryBuilder()
            ->insert('veelgesteldevragen')
            ->values(
                [
                    'vraag'    => ':question',
                    'antwoord' => ':answer',
                ]
            )
            ->setParameters(
                [
                    'question' => $frequentlyAskedQuestion->question(),
                    'answer'   => $frequentlyAskedQuestion->answer(),
                ]
            )
            ->execute();
    }

    public function update(FrequentlyAskedQuestion $frequentlyAskedQuestion): void
    {
        $this->connection->createQueryBuilder()
            ->update('veelgesteldevragen')
            ->set('vraag', ':question')
            ->set('antwoord', ':answer')
            ->where('id = :id')
            ->setParameters(
                [
                    'question' => $frequentlyAskedQuestion->question(),
                    'answer'   => $frequentlyAskedQuestion->answer(),
                    'id'       => $frequentlyAskedQuestion->id(),
                ]
            )
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('veelgesteldevragen')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
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
