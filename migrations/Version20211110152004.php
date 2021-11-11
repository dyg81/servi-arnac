<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211110152004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_expediente ADD deposito_id INT NOT NULL');
        $this->addSql('ALTER TABLE sac_expediente ADD CONSTRAINT FK_164CB7FD4140C3FC FOREIGN KEY (deposito_id) REFERENCES sac_deposito (id)');
        $this->addSql('CREATE INDEX IDX_164CB7FD4140C3FC ON sac_expediente (deposito_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_expediente DROP FOREIGN KEY FK_164CB7FD4140C3FC');
        $this->addSql('DROP INDEX IDX_164CB7FD4140C3FC ON sac_expediente');
        $this->addSql('ALTER TABLE sac_expediente DROP deposito_id');
    }
}
