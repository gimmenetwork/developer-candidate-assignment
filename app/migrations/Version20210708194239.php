<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210708194239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, taken_id INT DEFAULT NULL, name VARCHAR(128) NOT NULL, author VARCHAR(128) NOT NULL, genre VARCHAR(128) NOT NULL, UNIQUE INDEX UNIQ_CBE5A33111E19CE4 (taken_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE book_state (id INT AUTO_INCREMENT NOT NULL, book_id INT NOT NULL, reader_id INT NOT NULL, return_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, INDEX IDX_AD51C78216A2B381 (book_id), INDEX IDX_AD51C7821717D737 (reader_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reader (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(128) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE book ADD CONSTRAINT FK_CBE5A33111E19CE4 FOREIGN KEY (taken_id) REFERENCES book_state (id)');
        $this->addSql('ALTER TABLE book_state ADD CONSTRAINT FK_AD51C78216A2B381 FOREIGN KEY (book_id) REFERENCES book (id)');
        $this->addSql('ALTER TABLE book_state ADD CONSTRAINT FK_AD51C7821717D737 FOREIGN KEY (reader_id) REFERENCES reader (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE book_state DROP FOREIGN KEY FK_AD51C78216A2B381');
        $this->addSql('ALTER TABLE book DROP FOREIGN KEY FK_CBE5A33111E19CE4');
        $this->addSql('ALTER TABLE book_state DROP FOREIGN KEY FK_AD51C7821717D737');
        $this->addSql('DROP TABLE book');
        $this->addSql('DROP TABLE book_state');
        $this->addSql('DROP TABLE reader');
    }
}
