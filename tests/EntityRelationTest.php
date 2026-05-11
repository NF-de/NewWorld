<?php

namespace App\Tests;

use App\Entity\User;
use App\Entity\Entreprise;
use App\Entity\Produit;
use App\Entity\Categorie;
use App\Entity\Commande;
use App\Entity\Adresse;
use App\Entity\Prix;
use App\Entity\LigneCommande;
use App\Enum\UnitType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntityRelationTest extends KernelTestCase
{
    /**
     * Teste la relation Entreprise <-> Produit
     */
    public function testEntrepriseProduitRelation(): void
    {
        self::bootKernel();
        
        $entreprise = new Entreprise();
        $entreprise->setNom("Vergers du Sud");

        $produit = new Produit();
        $produit->setName("Abricots")
                ->setEntreprise($entreprise);

        // Vérification bidirectionnelle
        $this->assertCount(1, $entreprise->getProduits());
        $this->assertSame($entreprise, $produit->getEntreprise());
    }

    /**
     * Teste la relation Produit <-> Categorie (Many-to-Many)
     */
    public function testCategorieProduitRelation(): void
    {
        $produit = new Produit();
        $categorie = new Categorie();
        $categorie->setNom("Fruits");

        $produit->addCategorie($categorie);

        $this->assertContains($categorie, $produit->getCategorie());
        $this->assertContains($produit, $categorie->getProduits());
    }

    /**
     * Teste la relation User <-> Adresse <-> Commande
     */
    public function testUserFullWorkflowRelation(): void
    {
        $user = new User();
        $user->setEmail("client@newworld.fr");

        $adresse = new Adresse();
        $adresse->setRue("12 rue des Vergers")
                ->setVille("Gap")
                ->setPays("France")
                ->setCodePostal("05000");

        $user->setAdresseId($adresse);

        $commande = new Commande();
        // Suppression de setReference() qui n'existe pas dans l'entité
        $commande->setStatus("EN_ATTENTE")
                 ->setCreatedAt(new \DateTime())
                 ->setAdresse($adresse); // Commande a besoin d'une adresse d'après ton JoinColumn(nullable: false)
        
        $user->addCommande($commande);

        $this->assertCount(1, $user->getCommandes());
        $this->assertSame($user, $commande->getUser());
    }
    /**
     * Teste le tunnel d'achat complet : Commande -> LigneCommande -> Produit
     */
    public function testOrderItemsWorkflow(): void
    {
        // 1. On prépare le terrain
        $commande = new Commande();
        $commande->setStatus("PANIER")
                 ->setCreatedAt(new \DateTime());

        $produit = new Produit();
        $produit->setName("Pomme de terre");

        // 2. On crée la ligne de commande (le lien)
        $ligne = new LigneCommande();
        $ligne->setCount(5) // Ton entité utilise setCount()
              ->setProduit($produit)
              ->setCommande($commande);

        // 3. On ajoute la ligne à la commande (via la méthode de l'entité Commande)
        $commande->addLigneCommande($ligne);

        // --- ASSERTIONS ---
        
        // La commande doit avoir 1 ligne
        $this->assertCount(1, $commande->getLigneCommandes());
        
        // On vérifie que la donnée est cohérente
        $this->assertSame($produit, $commande->getLigneCommandes()->first()->getProduit());
        $this->assertEquals(5, $commande->getLigneCommandes()->first()->getCount());
        
        // Vérification de la relation inverse
        $this->assertSame($commande, $ligne->getCommande());
    }
    /**
     * Teste la relation Produit <-> Prix et la logique de calcul
     */
    public function testProduitPrixRelation(): void
    {
        $produit = new Produit();
        $produit->setName("Pommes Gala");

        $prix = new Prix();
        $prix->setValeurHT(2.50)
             ->setValeurTva(5.5)
             ->setProduit($produit);

        // Simulation d'un calcul (en attendant un LifecycleCallback)
        $ttc = $prix->getValeurHT() * (1 + $prix->getValeurTva() / 100);
        $prix->setValeurTTC($ttc);

        // 1. Vérifie que le produit contient bien le prix (bidirectionnel)
        $this->assertCount(1, $produit->getPrix());
        
        // 2. Vérifie que le prix pointe vers le bon produit
        $this->assertSame($produit, $prix->getProduit());

        // 3. Vérifie la précision des calculs (important pour les types Decimal)
        $this->assertEquals(2.64, round($prix->getValeurTTC(), 2));
    }
}