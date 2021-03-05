<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20210302073057 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $table = $schema->getTable('user_credentials');
        $table->dropColumn('set_password_token');
        $table->addColumn('password_token', Types::GUID, ['notNull' => false]);
    }

    public function down(Schema $schema) : void
    {
    }
}
