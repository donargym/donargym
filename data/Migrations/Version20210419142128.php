<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20210419142128 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('inschrijvingen');
        $table->addColumn('ooievaarspas', Types::BOOLEAN);
    }

    public function down(Schema $schema) : void
    {
    }
}
