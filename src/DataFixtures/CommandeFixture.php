<?php

namespace App\DataFixtures;

use App\Entity\Commande;
use App\Entity\User;
use App\Entity\Adresse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommandeFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/Commande.csv';

        if (!file_exists($file)) {
            throw new \Exception("Fichier CSV Commande introuvable : $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $commande = new Commande();
            $commande->setStatus($data['status']);
            
            // Dates
            $commande->setCreatedAt(
                !empty($data['created_at']) ? new \DateTime($data['created_at']) : new \DateTime()
            );
            
            $commande->setDateValidation(
                !empty($data['date_validation']) ? new \DateTime($data['date_validation']) : null
            );

            // Liaison avec l'Utilisateur (ManyToOne)
            // Note : le setter dans ton entité est setUser() avec une majuscule au paramètre
            $commande->setUser(
                $this->getReference('user_' . $data['user_id'], User::class)
            );

            // Liaison avec l'Adresse (OneToOne)
            $commande->setAdresse(
                $this->getReference('adresse_' . $data['adresse_id'], Adresse::class)
            );

            $manager->persist($commande);

            // Ajout d'une référence pour LigneCommandeFixture
            $this->addReference('commande_' . $data['id'], $commande);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class,
            AdresseFixture::class,
        ];
    }
}