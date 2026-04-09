<?php

namespace App\DataFixtures;

use App\Entity\Adresse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdresseFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/Adresse.csv';

        if (!file_exists($file)) {
            throw new \Exception("Le fichier CSV Adresse est introuvable : $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $adresse = new Adresse();
            $adresse->setRue($data['rue']);
            $adresse->setVille($data['ville']);
            $adresse->setCodePostal($data['code_postal']);
            $adresse->setPays($data['pays']);

            $manager->persist($adresse);

            // On crée la référence pour pouvoir lier cette adresse à un User ou une Commande
            $this->addReference('adresse_' . $data['id'], $adresse);
        }

        $manager->flush();
    }
}