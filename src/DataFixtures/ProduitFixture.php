<?php

namespace App\DataFixtures;

use App\Entity\Produit;
use App\Entity\Entreprise;
use App\Entity\Categorie;
use App\Enum\UnitType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProduitFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/Produit.csv';

        if (!file_exists($file)) {
            throw new \Exception("Le fichier CSV Produit est introuvable : $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $produit = new Produit();
            $produit->setName($data['name']);
            $produit->setDescription($data['description']);
            $produit->setQuantite((int)$data['quantite']);
            
            // --- AJOUT DE L'IMAGE ---
            // On vérifie si la colonne existe dans le CSV pour éviter une erreur
            if (!empty($data['image_name'])) {
                $produit->setImageName($data['image_name']);
            }

            // Gestion de l'Enum UnitType
            if (!empty($data['unit_type'])) {
                $produit->setUnitType(UnitType::from($data['unit_type']));
            }

            $produit->setCreatedAt(
                !empty($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable()
            );

            // Liaison avec l'Entreprise
            $produit->setEntreprise(
                $this->getReference('entreprise_' . $data['entreprise_id'], Entreprise::class)
            );

            // Liaison avec la Catégorie
            $produit->addCategorie(
                $this->getReference('categorie_' . $data['categorie_id'], Categorie::class)
            );

            $manager->persist($produit);

            $this->addReference('produit_' . $data['id'], $produit);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EntrepriseFixture::class,
            CategorieFixture::class,
        ];
    }
}