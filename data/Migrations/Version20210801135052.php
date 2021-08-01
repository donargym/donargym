<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210801135052 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $schema->dropTable('aanwezigheid');
        $schema->dropTable('about_competition_group_member');
        $schema->dropTable('account_phone_number');
        $schema->dropTable('afmeldingen');
        $schema->dropTable('cijfers');
        $schema->dropTable('competition_group');
        $schema->dropTable('competition_group_account');
        $schema->dropTable('competition_group_calendar');
        $schema->dropTable('competition_group_competition_result');
        $schema->dropTable('competition_group_member');
        $schema->dropTable('competition_group_member_role');
        $schema->dropTable('competition_group_member_training_participation');
        $schema->dropTable('competition_group_member_training_time_subscription');
        $schema->dropTable('competition_group_training');
        $schema->dropTable('competition_group_training_time');
        $schema->dropTable('doelen');
        $schema->dropTable('functie');
        $schema->dropTable('groepen');
        $schema->dropTable('inschrijvingen');
        $schema->dropTable('personen_trainingen');
        $schema->dropTable('personen_wedstrijdkalender');
        $schema->dropTable('persoon');
        $schema->dropTable('scores');
        $schema->dropTable('seizoensdoelen');
        $schema->dropTable('selectiefoto');
        $schema->dropTable('send_mail');
        $schema->dropTable('stukje');
        $schema->dropTable('subdoelen');
        $schema->dropTable('toegestane_niveaus');
        $schema->dropTable('trainingen');
        $schema->dropTable('trainingsdata');
        $schema->dropTable('trainingsplan');
        $schema->dropTable('trainingsstage');
        $schema->dropTable('trainingsstage_trainer');
        $schema->dropTable('turnster');
        $schema->dropTable('vereniging');
        $schema->dropTable('vloermuziek');
        $schema->dropTable('voedsel');
        $schema->dropTable('wedstrijdkalender');
        $schema->dropTable('wedstrijduitslagen');
    }

    public function down(Schema $schema) : void
    {
    }
}
