<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211112131257 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sac_libro (id INT AUTO_INCREMENT NOT NULL, deposito_id INT NOT NULL, fondo_id INT NOT NULL, estante_id INT NOT NULL, anaquel_id INT NOT NULL, tomo VARCHAR(5) NOT NULL, anno VARCHAR(4) NOT NULL, descripcion LONGTEXT NOT NULL, identificador VARCHAR(255) NOT NULL, INDEX IDX_E55BFDF74140C3FC (deposito_id), INDEX IDX_E55BFDF7AA510E89 (fondo_id), INDEX IDX_E55BFDF7B3E5B35F (estante_id), INDEX IDX_E55BFDF7FD9D1662 (anaquel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sac_libro ADD CONSTRAINT FK_E55BFDF74140C3FC FOREIGN KEY (deposito_id) REFERENCES sac_deposito (id)');
        $this->addSql('ALTER TABLE sac_libro ADD CONSTRAINT FK_E55BFDF7AA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_fondo (id)');
        $this->addSql('ALTER TABLE sac_libro ADD CONSTRAINT FK_E55BFDF7B3E5B35F FOREIGN KEY (estante_id) REFERENCES sac_estante (id)');
        $this->addSql('ALTER TABLE sac_libro ADD CONSTRAINT FK_E55BFDF7FD9D1662 FOREIGN KEY (anaquel_id) REFERENCES sac_anaquel (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE sac_libro');
    }
}
