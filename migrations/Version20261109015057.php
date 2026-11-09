<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Initial schema generated from the current Doctrine entity mappings.
 */
final class Version20261109015057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crea el esquema inicial de SAC ARNAC a partir de las entidades actuales.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE sac_registro_anaqueles (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(2) NOT NULL, slug VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_C1B9E877989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_registro_depositos (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(2) NOT NULL, slug VARCHAR(11) NOT NULL, UNIQUE INDEX UNIQ_37D15A5989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_registro_estantes (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(2) NOT NULL, slug VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_D9CD4CF4989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_registro_expedientes (id INT AUTO_INCREMENT NOT NULL, fondo_id INT NOT NULL, deposito_id INT NOT NULL, estante_id INT NOT NULL, anaquel_id INT NOT NULL, legajo_id INT NOT NULL, numero VARCHAR(4) NOT NULL, slug VARCHAR(255) NOT NULL, contenido LONGTEXT NOT NULL, estado SMALLINT NOT NULL, vitrina TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_EEE6A663989D9B62 (slug), INDEX IDX_EEE6A663AA510E89 (fondo_id), INDEX IDX_EEE6A6634140C3FC (deposito_id), INDEX IDX_EEE6A663B3E5B35F (estante_id), INDEX IDX_EEE6A663FD9D1662 (anaquel_id), INDEX IDX_EEE6A663602BF2CE (legajo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_registro_fondos (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, contenido LONGTEXT NOT NULL, contador INT NOT NULL, UNIQUE INDEX UNIQ_EFFA5A80989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_registro_fondos_depositos (fondo_id INT NOT NULL, deposito_id INT NOT NULL, INDEX IDX_3D2C0F01AA510E89 (fondo_id), INDEX IDX_3D2C0F014140C3FC (deposito_id), PRIMARY KEY(fondo_id, deposito_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_registro_legajos (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(3) NOT NULL, slug VARCHAR(10) NOT NULL, UNIQUE INDEX UNIQ_29C0C7AC989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_registro_libros (id INT AUTO_INCREMENT NOT NULL, fondo_id INT NOT NULL, deposito_id INT NOT NULL, estante_id INT NOT NULL, anaquel_id INT NOT NULL, tomo VARCHAR(3) NOT NULL, anno VARCHAR(4) NOT NULL, contenido LONGTEXT NOT NULL, slug VARCHAR(255) NOT NULL, estado SMALLINT NOT NULL, vitrina TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_935CEC3C989D9B62 (slug), INDEX IDX_935CEC3CAA510E89 (fondo_id), INDEX IDX_935CEC3C4140C3FC (deposito_id), INDEX IDX_935CEC3CB3E5B35F (estante_id), INDEX IDX_935CEC3CFD9D1662 (anaquel_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_cartas (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, documento VARCHAR(255) NOT NULL, estado SMALLINT NOT NULL, observaciones LONGTEXT DEFAULT NULL, fecha_solicitud DATETIME NOT NULL, fecha_respuesta DATETIME NOT NULL, fecha_vencimiento DATETIME NOT NULL, INDEX IDX_B8E4F05DE734E51 (cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_cartas_fondos (carta_id INT NOT NULL, fondo_id INT NOT NULL, INDEX IDX_4791E03546A559E1 (carta_id), INDEX IDX_4791E035AA510E89 (fondo_id), PRIMARY KEY(carta_id, fondo_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_centro_laboral (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_6FB5C296989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_clientes (id INT AUTO_INCREMENT NOT NULL, pais_id INT NOT NULL, centro_laboral_id INT NOT NULL, nombre VARCHAR(100) NOT NULL, identificacion VARCHAR(11) NOT NULL, direccion VARCHAR(255) NOT NULL, telefono VARCHAR(20) DEFAULT NULL, correo VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_321252884291D2B (identificacion), INDEX IDX_3212528C604D5C6 (pais_id), INDEX IDX_3212528F8959347 (centro_laboral_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_investigaciones (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, fondo_id INT NOT NULL, estado SMALLINT NOT NULL, fecha_solicitud DATETIME NOT NULL, fecha_actualizacion DATETIME NOT NULL, INDEX IDX_71F38B85DE734E51 (cliente_id), INDEX IDX_71F38B85AA510E89 (fondo_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_investigaciones_expedientes (servicio_id INT NOT NULL, expediente_id INT NOT NULL, INDEX IDX_71DFEAA871CAA3E7 (servicio_id), INDEX IDX_71DFEAA84BF37E4E (expediente_id), PRIMARY KEY(servicio_id, expediente_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_investigaciones_libros (servicio_id INT NOT NULL, libro_id INT NOT NULL, INDEX IDX_3C98845F71CAA3E7 (servicio_id), INDEX IDX_3C98845FC0238522 (libro_id), PRIMARY KEY(servicio_id, libro_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_paises (id INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_68F73C2F989D9B62 (slug), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_sala_vitrinas (id INT AUTO_INCREMENT NOT NULL, investigacion_id INT NOT NULL, documento_id INT NOT NULL, documento_tipo VARCHAR(3) NOT NULL, fecha_vencimiento DATETIME NOT NULL, documento_nombre VARCHAR(255) NOT NULL, INDEX IDX_E0C0B40C7E1D61C7 (investigacion_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_servicio_certificaciones (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, fecha_solicitud DATETIME NOT NULL, fecha_actualizacion DATETIME NOT NULL, documento_id INT NOT NULL, documento_nombre VARCHAR(255) NOT NULL, documento_fondo VARCHAR(255) NOT NULL, documento_tipo VARCHAR(3) NOT NULL, estado SMALLINT NOT NULL, INDEX IDX_B95DF160DE734E51 (cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_servicio_reprografias (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, fecha_solicitud DATETIME NOT NULL, fecha_actualizacion DATETIME NOT NULL, documento_id INT NOT NULL, documento_nombre VARCHAR(255) NOT NULL, documento_fondo VARCHAR(255) NOT NULL, documento_tipo VARCHAR(3) NOT NULL, estado SMALLINT NOT NULL, INDEX IDX_CECB1B32DE734E51 (cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_servicio_transcripciones (id INT AUTO_INCREMENT NOT NULL, cliente_id INT NOT NULL, fecha_solicitud DATETIME NOT NULL, fecha_actualizacion DATETIME NOT NULL, documento_id INT NOT NULL, estado SMALLINT NOT NULL, documento_nombre VARCHAR(255) NOT NULL, documento_tipo VARCHAR(3) NOT NULL, documento_fondo VARCHAR(255) NOT NULL, INDEX IDX_5E2F3805DE734E51 (cliente_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_trazas (id INT AUTO_INCREMENT NOT NULL, fecha DATETIME NOT NULL, usuario VARCHAR(255) NOT NULL, contenido LONGTEXT NOT NULL, tipo VARCHAR(10) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE sac_usuarios (id INT AUTO_INCREMENT NOT NULL, usuario VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', nombre VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, ruta VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_45B4B5DC2265B05D (usuario), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE sac_registro_expedientes ADD CONSTRAINT FK_EEE6A663AA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_registro_fondos (id)');
        $this->addSql('ALTER TABLE sac_registro_expedientes ADD CONSTRAINT FK_EEE6A6634140C3FC FOREIGN KEY (deposito_id) REFERENCES sac_registro_depositos (id)');
        $this->addSql('ALTER TABLE sac_registro_expedientes ADD CONSTRAINT FK_EEE6A663B3E5B35F FOREIGN KEY (estante_id) REFERENCES sac_registro_estantes (id)');
        $this->addSql('ALTER TABLE sac_registro_expedientes ADD CONSTRAINT FK_EEE6A663FD9D1662 FOREIGN KEY (anaquel_id) REFERENCES sac_registro_anaqueles (id)');
        $this->addSql('ALTER TABLE sac_registro_expedientes ADD CONSTRAINT FK_EEE6A663602BF2CE FOREIGN KEY (legajo_id) REFERENCES sac_registro_legajos (id)');
        $this->addSql('ALTER TABLE sac_registro_fondos_depositos ADD CONSTRAINT FK_3D2C0F01AA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_registro_fondos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sac_registro_fondos_depositos ADD CONSTRAINT FK_3D2C0F014140C3FC FOREIGN KEY (deposito_id) REFERENCES sac_registro_depositos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sac_registro_libros ADD CONSTRAINT FK_935CEC3CAA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_registro_fondos (id)');
        $this->addSql('ALTER TABLE sac_registro_libros ADD CONSTRAINT FK_935CEC3C4140C3FC FOREIGN KEY (deposito_id) REFERENCES sac_registro_depositos (id)');
        $this->addSql('ALTER TABLE sac_registro_libros ADD CONSTRAINT FK_935CEC3CB3E5B35F FOREIGN KEY (estante_id) REFERENCES sac_registro_estantes (id)');
        $this->addSql('ALTER TABLE sac_registro_libros ADD CONSTRAINT FK_935CEC3CFD9D1662 FOREIGN KEY (anaquel_id) REFERENCES sac_registro_anaqueles (id)');
        $this->addSql('ALTER TABLE sac_sala_cartas ADD CONSTRAINT FK_B8E4F05DE734E51 FOREIGN KEY (cliente_id) REFERENCES sac_sala_clientes (id)');
        $this->addSql('ALTER TABLE sac_sala_cartas_fondos ADD CONSTRAINT FK_4791E03546A559E1 FOREIGN KEY (carta_id) REFERENCES sac_sala_cartas (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sac_sala_cartas_fondos ADD CONSTRAINT FK_4791E035AA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_registro_fondos (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sac_sala_clientes ADD CONSTRAINT FK_3212528C604D5C6 FOREIGN KEY (pais_id) REFERENCES sac_sala_paises (id)');
        $this->addSql('ALTER TABLE sac_sala_clientes ADD CONSTRAINT FK_3212528F8959347 FOREIGN KEY (centro_laboral_id) REFERENCES sac_sala_centro_laboral (id)');
        $this->addSql('ALTER TABLE sac_sala_investigaciones ADD CONSTRAINT FK_71F38B85DE734E51 FOREIGN KEY (cliente_id) REFERENCES sac_sala_clientes (id)');
        $this->addSql('ALTER TABLE sac_sala_investigaciones ADD CONSTRAINT FK_71F38B85AA510E89 FOREIGN KEY (fondo_id) REFERENCES sac_registro_fondos (id)');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_expedientes ADD CONSTRAINT FK_71DFEAA871CAA3E7 FOREIGN KEY (servicio_id) REFERENCES sac_sala_investigaciones (id)');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_expedientes ADD CONSTRAINT FK_71DFEAA84BF37E4E FOREIGN KEY (expediente_id) REFERENCES sac_registro_expedientes (id)');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_libros ADD CONSTRAINT FK_3C98845F71CAA3E7 FOREIGN KEY (servicio_id) REFERENCES sac_sala_investigaciones (id)');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_libros ADD CONSTRAINT FK_3C98845FC0238522 FOREIGN KEY (libro_id) REFERENCES sac_registro_libros (id)');
        $this->addSql('ALTER TABLE sac_sala_vitrinas ADD CONSTRAINT FK_E0C0B40C7E1D61C7 FOREIGN KEY (investigacion_id) REFERENCES sac_sala_investigaciones (id)');
        $this->addSql('ALTER TABLE sac_servicio_certificaciones ADD CONSTRAINT FK_B95DF160DE734E51 FOREIGN KEY (cliente_id) REFERENCES sac_sala_clientes (id)');
        $this->addSql('ALTER TABLE sac_servicio_reprografias ADD CONSTRAINT FK_CECB1B32DE734E51 FOREIGN KEY (cliente_id) REFERENCES sac_sala_clientes (id)');
        $this->addSql('ALTER TABLE sac_servicio_transcripciones ADD CONSTRAINT FK_5E2F3805DE734E51 FOREIGN KEY (cliente_id) REFERENCES sac_sala_clientes (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE sac_registro_expedientes DROP FOREIGN KEY FK_EEE6A663AA510E89');
        $this->addSql('ALTER TABLE sac_registro_expedientes DROP FOREIGN KEY FK_EEE6A6634140C3FC');
        $this->addSql('ALTER TABLE sac_registro_expedientes DROP FOREIGN KEY FK_EEE6A663B3E5B35F');
        $this->addSql('ALTER TABLE sac_registro_expedientes DROP FOREIGN KEY FK_EEE6A663FD9D1662');
        $this->addSql('ALTER TABLE sac_registro_expedientes DROP FOREIGN KEY FK_EEE6A663602BF2CE');
        $this->addSql('ALTER TABLE sac_registro_fondos_depositos DROP FOREIGN KEY FK_3D2C0F01AA510E89');
        $this->addSql('ALTER TABLE sac_registro_fondos_depositos DROP FOREIGN KEY FK_3D2C0F014140C3FC');
        $this->addSql('ALTER TABLE sac_registro_libros DROP FOREIGN KEY FK_935CEC3CAA510E89');
        $this->addSql('ALTER TABLE sac_registro_libros DROP FOREIGN KEY FK_935CEC3C4140C3FC');
        $this->addSql('ALTER TABLE sac_registro_libros DROP FOREIGN KEY FK_935CEC3CB3E5B35F');
        $this->addSql('ALTER TABLE sac_registro_libros DROP FOREIGN KEY FK_935CEC3CFD9D1662');
        $this->addSql('ALTER TABLE sac_sala_cartas DROP FOREIGN KEY FK_B8E4F05DE734E51');
        $this->addSql('ALTER TABLE sac_sala_cartas_fondos DROP FOREIGN KEY FK_4791E03546A559E1');
        $this->addSql('ALTER TABLE sac_sala_cartas_fondos DROP FOREIGN KEY FK_4791E035AA510E89');
        $this->addSql('ALTER TABLE sac_sala_clientes DROP FOREIGN KEY FK_3212528C604D5C6');
        $this->addSql('ALTER TABLE sac_sala_clientes DROP FOREIGN KEY FK_3212528F8959347');
        $this->addSql('ALTER TABLE sac_sala_investigaciones DROP FOREIGN KEY FK_71F38B85DE734E51');
        $this->addSql('ALTER TABLE sac_sala_investigaciones DROP FOREIGN KEY FK_71F38B85AA510E89');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_expedientes DROP FOREIGN KEY FK_71DFEAA871CAA3E7');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_expedientes DROP FOREIGN KEY FK_71DFEAA84BF37E4E');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_libros DROP FOREIGN KEY FK_3C98845F71CAA3E7');
        $this->addSql('ALTER TABLE sac_sala_investigaciones_libros DROP FOREIGN KEY FK_3C98845FC0238522');
        $this->addSql('ALTER TABLE sac_sala_vitrinas DROP FOREIGN KEY FK_E0C0B40C7E1D61C7');
        $this->addSql('ALTER TABLE sac_servicio_certificaciones DROP FOREIGN KEY FK_B95DF160DE734E51');
        $this->addSql('ALTER TABLE sac_servicio_reprografias DROP FOREIGN KEY FK_CECB1B32DE734E51');
        $this->addSql('ALTER TABLE sac_servicio_transcripciones DROP FOREIGN KEY FK_5E2F3805DE734E51');
        $this->addSql('DROP TABLE sac_registro_anaqueles');
        $this->addSql('DROP TABLE sac_registro_depositos');
        $this->addSql('DROP TABLE sac_registro_estantes');
        $this->addSql('DROP TABLE sac_registro_expedientes');
        $this->addSql('DROP TABLE sac_registro_fondos');
        $this->addSql('DROP TABLE sac_registro_fondos_depositos');
        $this->addSql('DROP TABLE sac_registro_legajos');
        $this->addSql('DROP TABLE sac_registro_libros');
        $this->addSql('DROP TABLE sac_sala_cartas');
        $this->addSql('DROP TABLE sac_sala_cartas_fondos');
        $this->addSql('DROP TABLE sac_sala_centro_laboral');
        $this->addSql('DROP TABLE sac_sala_clientes');
        $this->addSql('DROP TABLE sac_sala_investigaciones');
        $this->addSql('DROP TABLE sac_sala_investigaciones_expedientes');
        $this->addSql('DROP TABLE sac_sala_investigaciones_libros');
        $this->addSql('DROP TABLE sac_sala_paises');
        $this->addSql('DROP TABLE sac_sala_vitrinas');
        $this->addSql('DROP TABLE sac_servicio_certificaciones');
        $this->addSql('DROP TABLE sac_servicio_reprografias');
        $this->addSql('DROP TABLE sac_servicio_transcripciones');
        $this->addSql('DROP TABLE sac_trazas');
        $this->addSql('DROP TABLE sac_usuarios');
    }
}
