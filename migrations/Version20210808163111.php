<?php

declare(strict_types=1);

namespace GimmeBook\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210808163111 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add authentication';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE refresh_token (uuid CHAR(36) NOT NULL COMMENT \'(DC2Type:guid)\', user_id INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_in INT NOT NULL, PRIMARY KEY(uuid)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reader ADD password VARCHAR(60) NOT NULL, ADD salt VARCHAR(60) NOT NULL, CHANGE name login VARCHAR(100) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CC3F893CAA08CB10 ON reader (login)');
        $this->addSql('CREATE INDEX login__idx ON reader (login)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE refresh_token');
        $this->addSql('DROP INDEX UNIQ_CC3F893CAA08CB10 ON reader');
        $this->addSql('DROP INDEX login__idx ON reader');
        $this->addSql('ALTER TABLE reader DROP password, DROP salt, CHANGE login name VARCHAR(100) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
