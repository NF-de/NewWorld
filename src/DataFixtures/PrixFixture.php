<?php

namespace App\DataFixtures;

use App\Entity\Prix;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class PrixFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/Prix.csv';

        if (!file_exists($file)) {
            throw new \Exception("Fichier CSV Prix introuvable : $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $prix = new Prix();
            
            $ht = (float)$data['valeurHT'];
            $tva = (float)$data['valeur_tva'];

            $prix->setValeurHT($ht);
            $prix->setValeurTva($tva);
            
            // Calcul du TTC : HT + TVA
            $prix->setValeurTTC($ht + $tva);

            // Liaison au produit via la référence créée dans ProduitFixture
            $prix->setProduit(
                $this->getReference('produit_' . $data['produit_id'], Produit::class)
            );

            $manager->persist($prix);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            ProduitFixture::class,
        ];
    }
}