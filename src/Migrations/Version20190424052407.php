<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190424052407 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE things (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(256) DEFAULT \'asdasd\' NOT NULL, name VARCHAR(256) DEFAULT NULL, password VARCHAR(256) DEFAULT NULL, email VARCHAR(256) DEFAULT NULL, from_date DATE DEFAULT NULL, is_admin TINYINT(1) DEFAULT NULL, type INT DEFAULT NULL, UNIQUE INDEX UNIQ_6FE451BDBF396750 (id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users CHANGE username username VARCHAR(256) DEFAULT NULL, CHANGE name name VARCHAR(256) DEFAULT NULL, CHANGE password password VARCHAR(256) DEFAULT NULL, CHANGE email email VARCHAR(256) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE things');
        $this->addSql('ALTER TABLE users CHANGE username username TEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE name name TEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE password password TEXT DEFAULT NULL COLLATE utf8_unicode_ci, CHANGE email email TEXT DEFAULT NULL COLLATE utf8_unicode_ci');
    }
}
