<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategorieFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/Categorie.csv';

        if (!file_exists($file)) {
            throw new \Exception("Le fichier CSV Categorie est introuvable : $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $categorie = new Categorie();
            $categorie->setNom($data['nom']);

            $manager->persist($categorie);

            // CRUCIAL : On crée la référence pour les produits
            // On utilise l'ID du CSV pour que la correspondance soit facile
            $this->addReference('categorie_' . $data['id'], $categorie);
        }

        $manager->flush();
    }
}