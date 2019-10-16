<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190406143507 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE municipality (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, about LONGTEXT NOT NULL COLLATE utf8_unicode_ci, mayor LONGTEXT NOT NULL COLLATE utf8_unicode_ci, featured_picture VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, web VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, email VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, phone VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, fax VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, map VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, extra_info LONGTEXT DEFAULT NULL COLLATE utf8_unicode_ci, facebook VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, twitter VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, instagram VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, whatsapp VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, mayor_photo VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE municipality_news (id INT AUTO_INCREMENT NOT NULL, municipality_id INT NOT NULL, title VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, content LONGTEXT NOT NULL COLLATE utf8_unicode_ci, featured_image VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, source_of_news_url VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, INDEX IDX_81A5E4A2AE6F181C (municipality_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE places_to_visit (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, about LONGTEXT NOT NULL COLLATE utf8_unicode_ci, maps VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, phone VARCHAR(50) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, web VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, email VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, address VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, featured_picture VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ptvcategory (id INT AUTO_INCREMENT NOT NULL, ptv_id INT NOT NULL, short_name VARCHAR(50) NOT NULL COLLATE utf8_unicode_ci, name VARCHAR(100) NOT NULL COLLATE utf8_unicode_ci, INDEX IDX_8AD0BA0A61E6090F (ptv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ptvcomment (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, comment LONGTEXT NOT NULL COLLATE utf8_unicode_ci, created DATETIME NOT NULL, INDEX IDX_DBAB6A327E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE ptvphoto (id INT AUTO_INCREMENT NOT NULL, ptv_id INT NOT NULL, file_name VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, date_added DATE NOT NULL, INDEX IDX_DEBB271861E6090F (ptv_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, username VARCHAR(255) NOT NULL COLLATE utf8_unicode_ci, password VARCHAR(64) NOT NULL COLLATE utf8_unicode_ci, roles LONGTEXT NOT NULL COLLATE utf8_unicode_ci COMMENT \'(DC2Type:array)\', UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE municipality');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE municipality_news');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE places_to_visit');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ptvcategory');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ptvcomment');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE ptvphoto');
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE users');
    }
}
