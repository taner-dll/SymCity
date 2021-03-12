<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210306123646 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE rememberme_token');
        $this->addSql('ALTER TABLE event ADD address VARCHAR(255) DEFAULT NULL, ADD maps_embed_str VARCHAR(255) DEFAULT NULL, ADD web VARCHAR(255) DEFAULT NULL, ADD phone VARCHAR(50) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE rememberme_token (series CHAR(88) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, value CHAR(88) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, lastUsed DATETIME NOT NULL, class VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, username VARCHAR(200) CHARACTER SET utf8 NOT NULL COLLATE `utf8_general_ci`, PRIMARY KEY(series)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event DROP address, DROP maps_embed_str, DROP web, DROP phone');
    }
}
