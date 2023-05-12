<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230512073642 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, candidate_id INT DEFAULT NULL, recruter_id INT DEFAULT NULL, date DATETIME NOT NULL, INDEX IDX_AC6340B391BD8781 (candidate_id), INDEX IDX_AC6340B3569B8235 (recruter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B391BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B3569B8235 FOREIGN KEY (recruter_id) REFERENCES recruter (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B391BD8781');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B3569B8235');
        $this->addSql('DROP TABLE `like`');
    }
}
