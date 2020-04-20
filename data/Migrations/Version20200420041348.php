<?php
declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20200420041348 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('competition_group');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('name', Types::STRING);
        $table->addColumn('sort_order', Types::INTEGER);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_member');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_account_id', Types::GUID);
        $table->addColumn('first_name', Types::STRING);
        $table->addColumn('last_name', Types::STRING);
        $table->addColumn('date_of_birth', Types::DATETIME_IMMUTABLE);
        $table->addColumn('picture_file_name', Types::STRING, ['notnull' => false]);
        $table->addColumn('floor_music_file_name', Types::STRING, ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('about_competition_group_member');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_member_id', Types::GUID);
        $table->addColumn('most_fun_apparatus', Types::TEXT, ['notnull' => false]);
        $table->addColumn('explanation_about_most_fun_apparatus', Types::TEXT, ['notnull' => false]);
        $table->addColumn('most_fun_competition', Types::TEXT, ['notnull' => false]);
        $table->addColumn('most_fun_or_hardest_skill', Types::TEXT, ['notnull' => false]);
        $table->addColumn('would_like_to_learn', Types::TEXT, ['notnull' => false]);
        $table->addColumn('example_gymnast', Types::TEXT, ['notnull' => false]);
        $table->addColumn('anything_else', Types::TEXT, ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_member_role');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_member_id', Types::GUID);
        $table->addColumn('competition_group_id', Types::GUID);
        $table->addColumn('role', Types::STRING);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_competition_result');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_id', Types::GUID);
        $table->addColumn('file_name', Types::STRING);
        $table->addColumn('competition_date', Types::STRING);
        $table->addColumn('name', Types::STRING);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_calendar');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_id', Types::GUID);
        $table->addColumn('file_name', Types::STRING);
        $table->addColumn('competition_date', Types::DATE_IMMUTABLE);
        $table->addColumn('name', Types::STRING);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_training_time');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_id', Types::GUID);
        $table->addColumn('training_day', Types::STRING);
        $table->addColumn('training_start_time', Types::TIME_IMMUTABLE);
        $table->addColumn('training_end_time', Types::TIME_IMMUTABLE);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_training');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_training_time_id', Types::GUID);
        $table->addColumn('training_date', Types::DATETIME_IMMUTABLE);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_member_training_time_subscription');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_training_time_id', Types::GUID);
        $table->addColumn('competition_group_member_id', Types::GUID);
        $table->setPrimaryKey(['id']);
        $table = $schema->createTable('competition_group_member_training_participation');
        $table->addColumn('id', Types::GUID);
        $table->addColumn('competition_group_member_id', Types::GUID);
        $table->addColumn('competition_group_training_id', Types::GUID);
        $table->addColumn('presence', Types::STRING);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
    }
}
