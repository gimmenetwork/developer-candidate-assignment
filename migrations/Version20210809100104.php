<?php

declare(strict_types=1);

namespace GimmeBook\Migration;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210809100104 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX device_id__idx ON refresh_token');
        $this->addSql('CREATE INDEX user_device__idx ON refresh_token (user_id, device_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX user_device__idx ON refresh_token');
        $this->addSql('CREATE INDEX device_id__idx ON refresh_token (device_id)');
    }
}
