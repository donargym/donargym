<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\Migrations\Exception\MigrationException;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200329193421 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE seizoensdoelen DROP FOREIGN KEY FK_A45472CB90FBB45F');
        $this->addSql('ALTER TABLE seizoensdoelen ADD groep_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE seizoensdoelen ADD CONSTRAINT FK_A45472CB9EB44EC5 FOREIGN KEY (groep_id) REFERENCES groepen (id)');
        $this->addSql('ALTER TABLE seizoensdoelen ADD CONSTRAINT FK_A45472CB90FBB45F FOREIGN KEY (persoon_id) REFERENCES persoon (id)');
        $this->addSql('CREATE INDEX IDX_A45472CB9EB44EC5 ON seizoensdoelen (groep_id)');
    }

    /**
     * @inheritDoc
     */
    public function down(Schema $schema): void
    {
        // TODO: Implement down() method.
    }
}
