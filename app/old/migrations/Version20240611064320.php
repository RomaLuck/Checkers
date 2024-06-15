<?php

declare(strict_types=1);

namespace old\migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611064320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP INDEX UNIQ_FF232B318F3B0C6D, ADD INDEX IDX_FF232B318F3B0C6D (black_user_id)');
        $this->addSql('ALTER TABLE games DROP INDEX UNIQ_FF232B319266829D, ADD INDEX IDX_FF232B319266829D (white_user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP INDEX IDX_FF232B319266829D, ADD UNIQUE INDEX UNIQ_FF232B319266829D (white_user_id)');
        $this->addSql('ALTER TABLE games DROP INDEX IDX_FF232B318F3B0C6D, ADD UNIQUE INDEX UNIQ_FF232B318F3B0C6D (black_user_id)');
    }
}
