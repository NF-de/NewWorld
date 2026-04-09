<?php

namespace App\DataFixtures;

use App\Entity\LigneCommande;
use App\Entity\Commande;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class LigneCommandeFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/LigneCommande.csv';

        if (!file_exists($file)) {
            throw new \Exception("Fichier CSV LigneCommande introuvable : $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $ligne = new LigneCommande();
            $ligne->setCount((int)$data['count']);

            // Liaison avec la Commande
            $ligne->setCommandeId(
                $this->getReference('commande_' . $data['commande_id'], Commande::class)
            );

            // Liaison avec le Produit
            $ligne->setProduitId(
                $this->getReference('produit_' . $data['produit_id'], Produit::class)
            );

            $manager->persist($ligne);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CommandeFixture::class,
            ProduitFixture::class,
        ];
    }
}