<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200419122714 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $table = $schema->createTable('user_credentials');
        $table->addColumn('username', Types::STRING, ['length' => 150]);
        $table->addColumn('encrypted_password', Types::STRING, ['notnull' => false]);
        $table->addColumn('user_id', Types::INTEGER);
        $table->addColumn('set_password_token', Types::STRING, ['notnull' => false]);
        $table->addColumn('token_expires_at', Types::DATETIME_IMMUTABLE, ['notnull' => false]);
        $table->setPrimaryKey(['username']);
    }

    public function down(Schema $schema) : void
    {
    }
}
