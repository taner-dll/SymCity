<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190406143537 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE places_to_visit CHANGE maps maps VARCHAR(255) DEFAULT NULL, CHANGE phone phone VARCHAR(50) DEFAULT NULL, CHANGE web web VARCHAR(100) DEFAULT NULL, CHANGE email email VARCHAR(100) DEFAULT NULL, CHANGE address address VARCHAR(255) DEFAULT NULL, CHANGE featured_picture featured_picture VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE municipality_news CHANGE featured_image featured_image VARCHAR(255) DEFAULT NULL, CHANGE source_of_news_url source_of_news_url VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE municipality CHANGE featured_picture featured_picture VARCHAR(255) DEFAULT NULL, CHANGE web web VARCHAR(100) DEFAULT NULL, CHANGE email email VARCHAR(100) DEFAULT NULL, CHANGE phone phone VARCHAR(50) DEFAULT NULL, CHANGE fax fax VARCHAR(50) DEFAULT NULL, CHANGE map map VARCHAR(255) DEFAULT NULL, CHANGE facebook facebook VARCHAR(255) DEFAULT NULL, CHANGE twitter twitter VARCHAR(255) DEFAULT NULL, CHANGE instagram instagram VARCHAR(255) DEFAULT NULL, CHANGE whatsapp whatsapp VARCHAR(255) DEFAULT NULL, CHANGE mayor_photo mayor_photo VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE municipality CHANGE featured_picture featured_picture VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE web web VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE phone phone VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE fax fax VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE map map VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE facebook facebook VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE twitter twitter VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE instagram instagram VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE whatsapp whatsapp VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE mayor_photo mayor_photo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE municipality_news CHANGE featured_image featured_image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE source_of_news_url source_of_news_url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
        $this->addSql('ALTER TABLE places_to_visit CHANGE maps maps VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE phone phone VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE web web VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE email email VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE address address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, CHANGE featured_picture featured_picture VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci');
    }
}
