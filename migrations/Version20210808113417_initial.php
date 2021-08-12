<?php

declare(strict_types=1);

namespace GimmeBook\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210808113417_initial extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial migrations';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE author (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX name__idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE book (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, year SMALLINT UNSIGNED NOT NULL, count_in_stock SMALLINT UNSIGNED NOT NULL, total_count SMALLINT UNSIGNED NOT NULL, available_for_leasing TINYINT(1) DEFAULT \'1\' NOT NULL, INDEX title__idx (title), INDEX year__idx (year), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE book_to_author (book_id INT NOT NULL, author_id INT NOT NULL, INDEX IDX_89D3C21316A2B381 (book_id), INDEX IDX_89D3C213F675F31B (author_id), PRIMARY KEY(book_id, author_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE book_to_genre (book_id INT NOT NULL, genre_id INT NOT NULL, INDEX IDX_9193149116A2B381 (book_id), INDEX IDX_919314914296D31F (genre_id), PRIMARY KEY(book_id, genre_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE genre (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, INDEX name__idx (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE lease (id INT AUTO_INCREMENT NOT NULL, book_id INT DEFAULT NULL, reader_id INT DEFAULT NULL, when_leased DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', when_to_return DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', when_returned DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX book__idx (book_id), INDEX reader__idx (reader_id), INDEX when_leased__idx (when_leased), INDEX when_returned__idx (when_returned), INDEX when_to_return__idx (when_to_return), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');

        $this->addSql('CREATE TABLE reader (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE author');

        $this->addSql('DROP TABLE book');

        $this->addSql('DROP TABLE book_to_author');

        $this->addSql('DROP TABLE book_to_genre');

        $this->addSql('DROP TABLE genre');

        $this->addSql('DROP TABLE lease');

        $this->addSql('DROP TABLE reader');
    }
}
