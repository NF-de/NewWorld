<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506082049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE date_validation date_validation DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE entreprise CHANGE date_validation date_validation DATETIME DEFAULT NULL, CHANGE date_archivage date_archivage DATETIME DEFAULT NULL, CHANGE date_mise_a_jour date_mise_a_jour DATETIME DEFAULT NULL, CHANGE date_fin date_fin DATETIME DEFAULT NULL, CHANGE date_pre_avis date_pre_avis DATETIME DEFAULT NULL, CHANGE cause_refus cause_refus VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE log CHANGE table_concernee table_concernee VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE prix CHANGE valeur_ttc valeur_ttc DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL, CHANGE nom nom VARCHAR(255) DEFAULT NULL, CHANGE prenom prenom VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande CHANGE date_validation date_validation DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE prix CHANGE valeur_ttc valeur_ttc DOUBLE PRECISION DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE messenger_messages CHANGE delivered_at delivered_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE entreprise CHANGE date_validation date_validation DATETIME DEFAULT \'NULL\', CHANGE date_archivage date_archivage DATETIME DEFAULT \'NULL\', CHANGE date_mise_a_jour date_mise_a_jour DATETIME DEFAULT \'NULL\', CHANGE date_fin date_fin DATETIME DEFAULT \'NULL\', CHANGE date_pre_avis date_pre_avis DATETIME DEFAULT \'NULL\', CHANGE cause_refus cause_refus VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE log CHANGE table_concernee table_concernee VARCHAR(255) DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE user CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_bin`, CHANGE nom nom VARCHAR(255) DEFAULT \'NULL\', CHANGE prenom prenom VARCHAR(255) DEFAULT \'NULL\', CHANGE last_login last_login DATETIME DEFAULT \'NULL\'');
    }
}
