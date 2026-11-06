<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220625210730 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_sala_clientes ADD centro_laboral_id INT NOT NULL');
        $this->addSql('ALTER TABLE sac_sala_clientes ADD CONSTRAINT FK_3212528F8959347 FOREIGN KEY (centro_laboral_id) REFERENCES sac_sala_centro_laboral (id)');
        $this->addSql('CREATE INDEX IDX_3212528F8959347 ON sac_sala_clientes (centro_laboral_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_sala_clientes DROP FOREIGN KEY FK_3212528F8959347');
        $this->addSql('DROP INDEX IDX_3212528F8959347 ON sac_sala_clientes');
        $this->addSql('ALTER TABLE sac_sala_clientes DROP centro_laboral_id');
    }
}
