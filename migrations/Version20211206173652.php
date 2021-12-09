<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211206173652 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sac_cliente (id INT AUTO_INCREMENT NOT NULL, pais_id INT NOT NULL, categoria_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, apellido VARCHAR(255) NOT NULL, identificacion VARCHAR(255) NOT NULL, direccion VARCHAR(255) NOT NULL, telefono VARCHAR(255) NOT NULL, correo VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B2FAF20084291D2B (identificacion), INDEX IDX_B2FAF200C604D5C6 (pais_id), INDEX IDX_B2FAF2003397707A (categoria_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sac_cliente ADD CONSTRAINT FK_B2FAF200C604D5C6 FOREIGN KEY (pais_id) REFERENCES sac_pais (id)');
        $this->addSql('ALTER TABLE sac_cliente ADD CONSTRAINT FK_B2FAF2003397707A FOREIGN KEY (categoria_id) REFERENCES sac_categoria (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sac_cliente');
    }
}
