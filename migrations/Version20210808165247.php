<?php

declare(strict_types=1);

namespace GimmeBook\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210808165247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Remove salt';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reader DROP salt, CHANGE password password VARCHAR(72) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE reader ADD salt VARCHAR(60) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`, CHANGE password password VARCHAR(60) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`');
    }
}
