<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200419190857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('competition_group_account');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('user_id', Types::INTEGER);
        $table->addColumn('street_house_number', Types::STRING);
        $table->addColumn('zip_code', Types::STRING);
        $table->addColumn('city', Types::STRING);
        $table->addColumn('created_at', Types::DATETIME_IMMUTABLE);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('account_phone_number');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('account_id', Types::GUID);
        $table->addColumn('phone_number', Types::STRING);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
    }
}
