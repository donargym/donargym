<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200403172027 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('nieuwsbericht');
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE);
    }

    public function down(Schema $schema) : void
    {
    }
}
