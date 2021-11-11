<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211110150925 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE sac_expediente (id INT AUTO_INCREMENT NOT NULL, fondo_id INT NOT NULL, legajo_id INT NOT NULL, estante_id INT NOT NULL, anaquel_id INT NOT NULL, numero VARCHAR(3) NOT NULL, identificador VARCHAR(255) NOT NULL, descripcion LONGTEXT NOT NULL, INDEX IDX_164CB7FDAA510E89 (fondo_id), INDEX IDX_164CB7FD602BF2CE (legajo_id), INDEX IDX_164CB7FDB3E5B35F (estante_id), INDEX IDX_164CB7FDFD9D1662 (anaquel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sac_expediente ADD CONSTRAINT FK_164CB7FDAA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_fondo (id)');
        $this->addSql('ALTER TABLE sac_expediente ADD CONSTRAINT FK_164CB7FD602BF2CE FOREIGN KEY (legajo_id) REFERENCES sac_legajo (id)');
        $this->addSql('ALTER TABLE sac_expediente ADD CONSTRAINT FK_164CB7FDB3E5B35F FOREIGN KEY (estante_id) REFERENCES sac_estante (id)');
        $this->addSql('ALTER TABLE sac_expediente ADD CONSTRAINT FK_164CB7FDFD9D1662 FOREIGN KEY (anaquel_id) REFERENCES sac_anaquel (id)');
        $this->addSql('DROP TABLE expedientes');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE expedientes (id INT AUTO_INCREMENT NOT NULL, fondo_id INT NOT NULL, legajo_id INT NOT NULL, estante_id INT NOT NULL, anaquel_id INT NOT NULL, numero VARCHAR(3) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, identificador VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, descripcion LONGTEXT CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_D59CA413FD9D1662 (anaquel_id), INDEX IDX_D59CA413AA510E89 (fondo_id), INDEX IDX_D59CA413602BF2CE (legajo_id), INDEX IDX_D59CA413B3E5B35F (estante_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE expedientes ADD CONSTRAINT FK_D59CA413602BF2CE FOREIGN KEY (legajo_id) REFERENCES sac_legajo (id)');
        $this->addSql('ALTER TABLE expedientes ADD CONSTRAINT FK_D59CA413AA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_fondo (id)');
        $this->addSql('ALTER TABLE expedientes ADD CONSTRAINT FK_D59CA413B3E5B35F FOREIGN KEY (estante_id) REFERENCES sac_estante (id)');
        $this->addSql('ALTER TABLE expedientes ADD CONSTRAINT FK_D59CA413FD9D1662 FOREIGN KEY (anaquel_id) REFERENCES sac_anaquel (id)');
        $this->addSql('DROP TABLE sac_expediente');
    }
}
