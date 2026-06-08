<?php

namespace App\Controller;

use App\Entity\Adresse;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class ApiPanierController extends AbstractController
{
    #[Route('/api/panier/add', name: 'api_panier_add', methods: ['POST'])]
    public function ajouterAuPanierOuValider(
        Request $request,
        ProduitRepository $produitRepository,
        CommandeRepository $commandeRepository,
        EntityManagerInterface $em
    ): JsonResponse {
        
        // 1. Récupérer l'utilisateur connecté via le Token JWT
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // 2. Récupérer les données de Flutter
        $data = json_decode($request->getContent(), true);

        // 3. Trouver le panier en cours (status = 'panier')
        $commandePanier = $commandeRepository->findOneBy([
            'User' => $user,
            'status' => 'panier'
        ]);

        // Si aucun panier n'existe, on le crée automatiquement
        if (!$commandePanier) {
            $commandePanier = new Commande();
            $commandePanier->setUser($user);
            $commandePanier->setStatus('panier');
            $commandePanier->setCreatedAt(new \DateTime());
            $em->persist($commandePanier);
        }

        // =====================================================================
        // ÉTAPE CIBLE : SI FLUTTER ENVOIE UNE ADRESSE, ON ENREGISTRE ET VALIDE
        // =====================================================================
        if (isset($data['rue']) || isset($data['ville']) || isset($data['codePostal'])) {
            if (!isset($data['rue'], $data['ville'], $data['codePostal'], $data['pays'])) {
                return new JsonResponse(['error' => 'Données d\'adresse incomplètes'], 400);
            }

            if ($commandePanier->getLigneCommandes()->isEmpty()) {
                return new JsonResponse(['error' => 'Impossible de valider un panier vide'], 400);
            }

            // === GESTION DES STOCKS : VÉRIFICATION FINALE AVANT VALIDATION ===
            foreach ($commandePanier->getLigneCommandes() as $ligne) {
                $produit = $ligne->getProduit();
                // CORRECTION : Utilisation de getQuantite() et getName()
                if ($produit->getQuantite() < $ligne->getCount()) {
                    return new JsonResponse([
                        'error' => sprintf('Le produit "%s" n\'a plus assez de stock (%d restants). Veuillez modifier votre panier.', $produit->getName(), $produit->getQuantite())
                    ], 400);
                }
            }

            // === GESTION DES STOCKS : SOUSTRACTION DES STOCKS EN BDD ===
            foreach ($commandePanier->getLigneCommandes() as $ligne) {
                $produit = $ligne->getProduit();
                // CORRECTION : Soustraction basée sur ton champ $quantite
                $nouvelleQuantiteStock = $produit->getQuantite() - $ligne->getCount();
                $produit->setQuantite($nouvelleQuantiteStock);
            }

            // Création et hydratation de l'entité Adresse
            $adresse = new Adresse();
            $adresse->setRue($data['rue']);
            $adresse->setVille($data['ville']);
            $adresse->setCodePostal($data['codePostal']);
            $adresse->setPays($data['pays']);
            $em->persist($adresse);

            // Liaison à la commande et changement de statut pour geler le panier
            $commandePanier->setAdresse($adresse);
            $commandePanier->setStatus('valide'); 
            $commandePanier->setDateValidation(new \DateTime());

            $em->flush();

            return new JsonResponse([
                'status' => 'Commande validée et stocks mis à jour avec succès !',
                'commande_id' => $commandePanier->getId()
            ], 200);
        }

        // =====================================================================
        // ÉTAPE DE BASE : AJOUT CLASSIQUE DE PRODUIT AU PANIER
        // =====================================================================
        $produitId = $data['produit_id'] ?? null;
        $quantite = $data['quantite'] ?? 1; // Le nombre d'articles ajoutés

        if (!$produitId) {
            return new JsonResponse(['error' => 'Données incomplètes (produit_id manquant)'], 400);
        }

        $produit = $produitRepository->find($produitId);
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit introuvable'], 404);
        }

        // Parcourir les lignes existantes pour voir si le produit y est déjà
        $ligneExistante = null;
        foreach ($commandePanier->getLigneCommandes() as $ligne) {
            if ($ligne->getProduit()->getId() === $produit->getId()) {
                $ligneExistante = $ligne;
                break;
            }
        }

        // === GESTION DES STOCKS : VÉRIFICATION À L'AJOUT AU PANIER ===
        $quantiteTotaleSouhaitee = $quantite;
        if ($ligneExistante) {
            $quantiteTotaleSouhaitee += $ligneExistante->getCount();
        }

        // CORRECTION : Utilisation de getQuantite()
        if ($produit->getQuantite() < $quantiteTotaleSouhaitee) {
            return new JsonResponse([
                'error' => sprintf('Stock insuffisant. Il reste %d unité(s) disponible(s). Vous en avez déjà %d dans votre panier.', 
                    $produit->getQuantite(),
                    $ligneExistante ? $ligneExistante->getCount() : 0
                )
            ], 400);
        }

        if ($ligneExistante) {
            // Le produit est déjà là, on incrémente son compteur
            $ligneExistante->setCount($ligneExistante->getCount() + $quantite);
        } else {
            // Nouveau produit : on crée une LigneCommande dédiée
            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commandePanier);
            $ligneCommande->setProduit($produit);
            $ligneCommande->setCount($quantite);
            $em->persist($ligneCommande);
        }

        $em->flush();

        return new JsonResponse(['status' => 'Produit ajouté au panier avec succès !'], 200);
    }
}