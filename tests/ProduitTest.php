<?php

namespace App\Tests;

use App\Entity\Produit;
use App\Entity\Prix;
use App\Entity\Categorie;
use App\Entity\Entreprise;
use App\Enum\UnitType;
use PHPUnit\Framework\TestCase;

class ProduitTest extends TestCase
{
    public function testValideProduitEntity(): void
    {
        $produit = new Produit();
        $date = new \DateTimeImmutable();
        $entreprise = new Entreprise();

        // On teste les Setters
        $produit->setName("Pommes Gala")
                ->setDescription("Belles pommes rouges")
                ->setQuantite(50)
                ->setUnitType(UnitType::KG)
                ->setCreatedAt($date)
                ->setEntreprise($entreprise);

        // Assertions pour vérifier que les Getters renvoient la bonne chose
        $this->assertEquals("Pommes Gala", $produit->getName());
        $this->assertEquals("Belles pommes rouges", $produit->getDescription());
        $this->assertEquals(50, $produit->getQuantite());
        $this->assertEquals(UnitType::KG, $produit->getUnitType());
        $this->assertEquals($date, $produit->getCreatedAt());
        $this->assertSame($entreprise, $produit->getEntreprise());
    }

    public function testAddRemovePrix(): void
    {
        $produit = new Produit();
        $prix = new Prix();

        // Au début, la collection doit être vide
        $this->assertCount(0, $produit->getPrix());

        // On ajoute un prix
        $produit->addPrix($prix);
        $this->assertCount(1, $produit->getPrix());
        $this->assertSame($produit, $prix->getProduit()); // Vérifie la relation inverse

        // On retire le prix
        $produit->removePrix($prix);
        $this->assertCount(0, $produit->getPrix());
    }

    public function testAddRemoveCategorie(): void
    {
        $produit = new Produit();
        $categorie = new Categorie();

        $produit->addCategorie($categorie);
        $this->assertContains($categorie, $produit->getCategorie());

        $produit->removeCategorie($categorie);
        $this->assertNotContains($categorie, $produit->getCategorie());
    }
}