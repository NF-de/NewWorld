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
            
            // Gestion de l'Enum UnitType
            // On utilise UnitType::from() ou UnitType::tryFrom() selon ton Enum
            if (!empty($data['unit_type'])) {
                $produit->setUnitType(UnitType::from($data['unit_type']));
            }

            $produit->setCreatedAt(
                !empty($data['created_at']) ? new \DateTimeImmutable($data['created_at']) : new \DateTimeImmutable()
            );

            // Liaison avec l'Entreprise (ManyToOne)
            $produit->setEntreprise(
                $this->getReference('entreprise_' . $data['entreprise_id'], Entreprise::class)
            );

            // Liaison avec la Catégorie (ManyToMany)
            // On récupère la catégorie via sa référence et on l'ajoute à la collection
            $produit->addCategorie(
                $this->getReference('categorie_' . $data['categorie_id'], Categorie::class)
            );

            $manager->persist($produit);

            // On crée une référence pour pouvoir lier des Prix ou des Lignes de Commande plus tard
            $this->addReference('produit_' . $data['id'], $produit);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            EntrepriseFixture::class,
            CategorieFixture::class, // Tu dois créer une CategorieFixture qui fait un addReference('categorie_ID')
        ];
    }
}