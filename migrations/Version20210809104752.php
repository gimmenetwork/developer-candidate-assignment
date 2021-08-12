<?php

declare(strict_types=1);

namespace GimmeBook\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210809104752 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add roles';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reader ADD role_id SMALLINT NOT NULL');

        $this->addSql(
            <<<SQL
INSERT INTO role (id) VALUE (NULL); /*READER role*/
INSERT INTO role (id) VALUE (NULL); /*ADMIN role*/
SQL
        );

        // add admin user admin:123321
        $this->addSql('INSERT INTO reader (login, password, role_id) VALUE ("admin", "$2y$10$4tDrzKXxsexmIJHrRix4GO5P.ENOfPy.eZ2yTa9PeUcWnxoc/C9Ke", 2)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE role');
        $this->addSql('ALTER TABLE reader DROP role_id');
    }
}
