<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211219150825 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sac_carta (id INT AUTO_INCREMENT NOT NULL, ciente_id INT NOT NULL, documento VARCHAR(255) NOT NULL, estado TINYINT(1) NOT NULL, fecha_solicitud DATE NOT NULL, INDEX IDX_F7B6B383E99422B (ciente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sac_carta ADD CONSTRAINT FK_F7B6B383E99422B FOREIGN KEY (ciente_id) REFERENCES sac_cliente (id)');
        $this->addSql('DROP TABLE carta');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE carta (id INT AUTO_INCREMENT NOT NULL, ciente_id INT NOT NULL, documento VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, estado TINYINT(1) NOT NULL, fecha_solicitud DATE NOT NULL, INDEX IDX_BDB93BE43E99422B (ciente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE carta ADD CONSTRAINT FK_BDB93BE43E99422B FOREIGN KEY (ciente_id) REFERENCES sac_cliente (id)');
        $this->addSql('DROP TABLE sac_carta');
    }
}
