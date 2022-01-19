<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211219152327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_carta DROP FOREIGN KEY FK_F7B6B383E99422B');
        $this->addSql('DROP INDEX IDX_F7B6B383E99422B ON sac_carta');
        $this->addSql('ALTER TABLE sac_carta CHANGE ciente_id cliente_id INT NOT NULL');
        $this->addSql('ALTER TABLE sac_carta ADD CONSTRAINT FK_F7B6B38DE734E51 FOREIGN KEY (cliente_id) REFERENCES sac_cliente (id)');
        $this->addSql('CREATE INDEX IDX_F7B6B38DE734E51 ON sac_carta (cliente_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_carta DROP FOREIGN KEY FK_F7B6B38DE734E51');
        $this->addSql('DROP INDEX IDX_F7B6B38DE734E51 ON sac_carta');
        $this->addSql('ALTER TABLE sac_carta CHANGE cliente_id ciente_id INT NOT NULL');
        $this->addSql('ALTER TABLE sac_carta ADD CONSTRAINT FK_F7B6B383E99422B FOREIGN KEY (ciente_id) REFERENCES sac_cliente (id)');
        $this->addSql('CREATE INDEX IDX_F7B6B383E99422B ON sac_carta (ciente_id)');
    }
}
