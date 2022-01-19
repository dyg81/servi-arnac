<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220111211410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_categoria ADD transcripcion_precio DOUBLE PRECISION NOT NULL, ADD reprografia_normal_precio DOUBLE PRECISION NOT NULL, ADD reprografia_grande_precio DOUBLE PRECISION NOT NULL, DROP investigacion_precio, DROP reprografia_precio');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sac_categoria ADD investigacion_precio DOUBLE PRECISION NOT NULL, ADD reprografia_precio DOUBLE PRECISION NOT NULL, DROP transcripcion_precio, DROP reprografia_normal_precio, DROP reprografia_grande_precio');
    }
}
