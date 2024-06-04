<?php

declare(strict_types=1);

namespace Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240604175651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, `table` VARCHAR(255) NOT NULL, white_user_id INT DEFAULT NULL, black_user_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_FF232B319266829D (white_user_id), UNIQUE INDEX UNIQ_FF232B318F3B0C6D (black_user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B319266829D FOREIGN KEY (white_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B318F3B0C6D FOREIGN KEY (black_user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B319266829D');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B318F3B0C6D');
        $this->addSql('DROP TABLE games');
    }
}
