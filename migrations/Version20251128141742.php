<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251128141742 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE entreprise (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(5) NOT NULL, siret VARCHAR(14) NOT NULL, status VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(20) NOT NULL, date_validation DATETIME NOT NULL, date_archivage DATETIME NOT NULL, date_mise_a_jour DATETIME NOT NULL, CONSTRAINT FK_D19FA60A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D19FA60A76ED395 ON entreprise (user_id)');
        $this->addSql('CREATE TABLE log (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, entreprise_id INTEGER DEFAULT NULL, operation VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, table_concernee VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_8F3F68C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_8F3F68C5A4AEAFEA FOREIGN KEY (entreprise_id) REFERENCES entreprise (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_8F3F68C5A76ED395 ON log (user_id)');
        $this->addSql('CREATE INDEX IDX_8F3F68C5A4AEAFEA ON log (entreprise_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, nom VARCHAR(255) DEFAULT NULL, prenom VARCHAR(255) DEFAULT NULL, created_at DATE NOT NULL, updated_at DATE NOT NULL, last_login DATETIME DEFAULT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE entreprise');
        $this->addSql('DROP TABLE log');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
