<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260410085956 extends AbstractMigration
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
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74B4FD8F9C3');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74B462C4194');
        $this->addSql('DROP INDEX UNIQ_3170B74B4FD8F9C3 ON ligne_commande');
        $this->addSql('DROP INDEX IDX_3170B74B462C4194 ON ligne_commande');
        $this->addSql('ALTER TABLE ligne_commande ADD produit_id INT NOT NULL, ADD commande_id INT DEFAULT NULL, DROP produit_id_id, DROP commande_id_id');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74BF347EFB FOREIGN KEY (produit_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74B82EA2E54 FOREIGN KEY (commande_id) REFERENCES commande (id)');
        $this->addSql('CREATE INDEX IDX_3170B74BF347EFB ON ligne_commande (produit_id)');
        $this->addSql('CREATE INDEX IDX_3170B74B82EA2E54 ON ligne_commande (commande_id)');
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
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74BF347EFB');
        $this->addSql('ALTER TABLE ligne_commande DROP FOREIGN KEY FK_3170B74B82EA2E54');
        $this->addSql('DROP INDEX IDX_3170B74BF347EFB ON ligne_commande');
        $this->addSql('DROP INDEX IDX_3170B74B82EA2E54 ON ligne_commande');
        $this->addSql('ALTER TABLE ligne_commande ADD commande_id_id INT DEFAULT NULL, DROP produit_id, CHANGE commande_id produit_id_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74B4FD8F9C3 FOREIGN KEY (produit_id_id) REFERENCES produit (id)');
        $this->addSql('ALTER TABLE ligne_commande ADD CONSTRAINT FK_3170B74B462C4194 FOREIGN KEY (commande_id_id) REFERENCES commande (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3170B74B4FD8F9C3 ON ligne_commande (produit_id_id)');
        $this->addSql('CREATE INDEX IDX_3170B74B462C4194 ON ligne_commande (commande_id_id)');
    }
}
