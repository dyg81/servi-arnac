<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211219213212 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carta_fondo (carta_id INT NOT NULL, fondo_id INT NOT NULL, INDEX IDX_5120F62946A559E1 (carta_id), INDEX IDX_5120F629AA510E89 (fondo_id), PRIMARY KEY(carta_id, fondo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE carta_fondo ADD CONSTRAINT FK_5120F62946A559E1 FOREIGN KEY (carta_id) REFERENCES sac_carta (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE carta_fondo ADD CONSTRAINT FK_5120F629AA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_fondo (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE carta_fondo');
    }
}
