<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506083641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prix CHANGE valeur_ht valeur_ht NUMERIC(10, 2) NOT NULL, CHANGE valeur_ttc valeur_ttc NUMERIC(10, 2) DEFAULT NULL, CHANGE valeur_tva valeur_tva NUMERIC(5, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prix CHANGE valeur_ht valeur_ht DOUBLE PRECISION NOT NULL, CHANGE valeur_ttc valeur_ttc DOUBLE PRECISION DEFAULT NULL, CHANGE valeur_tva valeur_tva DOUBLE PRECISION NOT NULL');
    }
}
