<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190524040405 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE things (user_rel INT NOT NULL, id INT AUTO_INCREMENT NOT NULL, username VARCHAR(256) DEFAULT \'asdasd\' NOT NULL, name VARCHAR(256) DEFAULT NULL, password VARCHAR(256) DEFAULT NULL, email VARCHAR(256) DEFAULT NULL, from_date DATE DEFAULT NULL, is_admin TINYINT(1) DEFAULT NULL, type INT DEFAULT NULL, UNIQUE INDEX UNIQ_6FE451BDBF396750 (id), INDEX IDX_6FE451BD1FACB636 (user_rel)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(256) NOT NULL, name VARCHAR(256) NOT NULL, hash VARCHAR(128) DEFAULT NULL, email VARCHAR(256) DEFAULT NULL, from_date DATE DEFAULT NULL, is_admin TINYINT(1) DEFAULT NULL, type INT DEFAULT NULL, UNIQUE INDEX UNIQ_1483A5E9BF396750 (id), UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE things ADD CONSTRAINT FK_6FE451BD1FACB636 FOREIGN KEY (user_rel) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE things DROP FOREIGN KEY FK_6FE451BD1FACB636');
        $this->addSql('DROP TABLE things');
        $this->addSql('DROP TABLE users');
    }
}
