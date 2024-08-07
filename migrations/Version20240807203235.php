<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240807203235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE games (id INT AUTO_INCREMENT NOT NULL, white_user_id INT DEFAULT NULL, black_user_id INT DEFAULT NULL, winner_id INT DEFAULT NULL, table_data JSON NOT NULL, room_id VARCHAR(255) NOT NULL, is_active TINYINT(1) NOT NULL, strategy_id SMALLINT NOT NULL, current_turn TINYINT(1) NOT NULL, INDEX IDX_FF232B319266829D (white_user_id), INDEX IDX_FF232B318F3B0C6D (black_user_id), INDEX IDX_FF232B315DFCD4B8 (winner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, oauth_id VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B319266829D FOREIGN KEY (white_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B318F3B0C6D FOREIGN KEY (black_user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE games ADD CONSTRAINT FK_FF232B315DFCD4B8 FOREIGN KEY (winner_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B319266829D');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B318F3B0C6D');
        $this->addSql('ALTER TABLE games DROP FOREIGN KEY FK_FF232B315DFCD4B8');
        $this->addSql('DROP TABLE games');
        $this->addSql('DROP TABLE users');
    }
}
