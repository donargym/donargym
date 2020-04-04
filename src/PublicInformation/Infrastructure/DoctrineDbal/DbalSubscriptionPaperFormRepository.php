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

    private function hydrate(array $row): SubscriptionPaperForm
    {
        return SubscriptionPaperForm::createFromDataSource(
            (int) $row['id'],
            $row['naam'],
            $row['locatie']
        );
    }
}
