<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211107121249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE archivage_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE etat_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE lieu_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE site_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE sortie_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE utilisateur_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE archivage (id INT NOT NULL, date_dernier_archivage TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE etat (id INT NOT NULL, libelle VARCHAR(30) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE lieu (id INT NOT NULL, nom VARCHAR(255) NOT NULL, rue VARCHAR(255) DEFAULT NULL, latitude DOUBLE PRECISION DEFAULT NULL, longitude DOUBLE PRECISION DEFAULT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(10) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE site (id INT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE sortie (id INT NOT NULL, lieu_id INT NOT NULL, organisateur_id INT NOT NULL, etat_id INT NOT NULL, site_id INT NOT NULL, nom VARCHAR(255) NOT NULL, date_heure_debut TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, duree INT NOT NULL, date_limite_inscription TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, nb_insciptions_max INT NOT NULL, infos_sortie TEXT NOT NULL, motif VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3C3FD3F26AB213CC ON sortie (lieu_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D936B2FA ON sortie (organisateur_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2D5E86FF ON sortie (etat_id)');
        $this->addSql('CREATE INDEX IDX_3C3FD3F2F6BD1646 ON sortie (site_id)');
        $this->addSql('CREATE TABLE utilisateur (id INT NOT NULL, site_id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, pseudo VARCHAR(20) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1D1C63B3E7927C74 ON utilisateur (email)');
        $this->addSql('CREATE INDEX IDX_1D1C63B3F6BD1646 ON utilisateur (site_id)');
        $this->addSql('CREATE TABLE utilisateur_sortie (utilisateur_id INT NOT NULL, sortie_id INT NOT NULL, PRIMARY KEY(utilisateur_id, sortie_id))');
        $this->addSql('CREATE INDEX IDX_5D92E979FB88E14F ON utilisateur_sortie (utilisateur_id)');
        $this->addSql('CREATE INDEX IDX_5D92E979CC72D953 ON utilisateur_sortie (sortie_id)');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F26AB213CC FOREIGN KEY (lieu_id) REFERENCES lieu (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D936B2FA FOREIGN KEY (organisateur_id) REFERENCES utilisateur (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2D5E86FF FOREIGN KEY (etat_id) REFERENCES etat (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE sortie ADD CONSTRAINT FK_3C3FD3F2F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur ADD CONSTRAINT FK_1D1C63B3F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur_sortie ADD CONSTRAINT FK_5D92E979FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE utilisateur_sortie ADD CONSTRAINT FK_5D92E979CC72D953 FOREIGN KEY (sortie_id) REFERENCES sortie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE sortie DROP CONSTRAINT FK_3C3FD3F2D5E86FF');
        $this->addSql('ALTER TABLE sortie DROP CONSTRAINT FK_3C3FD3F26AB213CC');
        $this->addSql('ALTER TABLE sortie DROP CONSTRAINT FK_3C3FD3F2F6BD1646');
        $this->addSql('ALTER TABLE utilisateur DROP CONSTRAINT FK_1D1C63B3F6BD1646');
        $this->addSql('ALTER TABLE utilisateur_sortie DROP CONSTRAINT FK_5D92E979CC72D953');
        $this->addSql('ALTER TABLE sortie DROP CONSTRAINT FK_3C3FD3F2D936B2FA');
        $this->addSql('ALTER TABLE utilisateur_sortie DROP CONSTRAINT FK_5D92E979FB88E14F');
        $this->addSql('DROP SEQUENCE archivage_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE etat_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE lieu_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE site_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE sortie_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE utilisateur_id_seq CASCADE');
        $this->addSql('DROP TABLE archivage');
        $this->addSql('DROP TABLE etat');
        $this->addSql('DROP TABLE lieu');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE sortie');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE utilisateur_sortie');
    }
}
