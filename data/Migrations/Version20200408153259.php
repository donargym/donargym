<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200408153259 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('inschrijvingen');
        $table->addColumn('subscribed_at', Types::DATETIME_IMMUTABLE);
    }

    public function down(Schema $schema) : void
    {
    }
}
