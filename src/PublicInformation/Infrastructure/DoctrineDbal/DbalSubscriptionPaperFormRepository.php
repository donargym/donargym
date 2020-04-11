<?php

declare(strict_types=1);

namespace App\PublicInformation\Infrastructure\DoctrineDbal;

use App\PublicInformation\Domain\Subscription\SubscriptionPaperForm;
use App\PublicInformation\Domain\Subscription\SubscriptionPaperForms;
use Doctrine\DBAL\Connection;
use PDO;

final class DbalSubscriptionPaperFormRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function find(int $id): ?SubscriptionPaperForm
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('formulieren')
            ->andWhere('id = :id')
            ->setParameter('id', $id)
            ->execute();

        $row = $statement->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    public function findAll(): SubscriptionPaperForms
    {
        $statement = $this->connection->createQueryBuilder()
            ->select('*')
            ->from('formulieren')
            ->execute();

        $subscriptionPaperForms = [];
        while (($row = $statement->fetch(PDO::FETCH_ASSOC)) !== false) {
            $subscriptionPaperForms[] = $this->hydrate($row);
        }

        return SubscriptionPaperForms::fromArray($subscriptionPaperForms);
    }

    public function remove(int $id): void
    {
        $this->connection->createQueryBuilder()
            ->delete('formulieren')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    private function hydrate(array $row): SubscriptionPaperForm
    {
        return SubscriptionPaperForm::createFromDataSource(
            (int) $row['id'],
            $row['naam'],
            $row['locatie']
        );
    }
}
