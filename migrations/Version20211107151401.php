<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211107151401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sac_fondo (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, descripcion LONGTEXT NOT NULL, identificador VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_9F02F639A8255881 (identificador), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE fondo_deposito (fondo_id INT NOT NULL, deposito_id INT NOT NULL, INDEX IDX_15F3267DAA510E89 (fondo_id), INDEX IDX_15F3267D4140C3FC (deposito_id), PRIMARY KEY(fondo_id, deposito_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fondo_deposito ADD CONSTRAINT FK_15F3267DAA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_fondo (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fondo_deposito ADD CONSTRAINT FK_15F3267D4140C3FC FOREIGN KEY (deposito_id) REFERENCES sac_deposito (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fondo_deposito DROP FOREIGN KEY FK_15F3267DAA510E89');
        $this->addSql('DROP TABLE sac_fondo');
        $this->addSql('DROP TABLE fondo_deposito');
    }
}
