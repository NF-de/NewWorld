<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251212130322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__entreprise AS SELECT id, user_id, nom, adresse, ville, code_postal, siret, status, email, telephone, date_validation, date_archivage, date_mise_a_jour FROM entreprise');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('CREATE TABLE entreprise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(5) NOT NULL, siret VARCHAR(14) NOT NULL, status VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, date_validation DATETIME DEFAULT NULL, date_archivage DATETIME DEFAULT NULL, date_mise_a_jour DATETIME DEFAULT NULL, CONSTRAINT FK_D19FA60A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO entreprise (id, user_id, nom, adresse, ville, code_postal, siret, status, email, telephone, date_validation, date_archivage, date_mise_a_jour) SELECT id, user_id, nom, adresse, ville, code_postal, siret, status, email, telephone, date_validation, date_archivage, date_mise_a_jour FROM __temp__entreprise');
        $this->addSql('DROP TABLE __temp__entreprise');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA60A76ED395 ON entreprise (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__entreprise AS SELECT id, user_id, nom, adresse, ville, code_postal, siret, status, email, telephone, date_validation, date_archivage, date_mise_a_jour FROM entreprise');
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('CREATE TABLE entreprise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(5) NOT NULL, siret VARCHAR(14) NOT NULL, status VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, date_validation DATETIME NOT NULL, date_archivage DATETIME NOT NULL, date_mise_a_jour DATETIME NOT NULL, CONSTRAINT FK_D19FA60A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO entreprise (id, user_id, nom, adresse, ville, code_postal, siret, status, email, telephone, date_validation, date_archivage, date_mise_a_jour) SELECT id, user_id, nom, adresse, ville, code_postal, siret, status, email, telephone, date_validation, date_archivage, date_mise_a_jour FROM __temp__entreprise');
        $this->addSql('DROP TABLE __temp__entreprise');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA60A76ED395 ON entreprise (user_id)');
    }
}
