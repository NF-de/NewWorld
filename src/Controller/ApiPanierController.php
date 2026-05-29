<?php

namespace App\Controller;

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
    public function ajouterAuPanier(
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
        $produitId = $data['produit_id'] ?? null;
        $quantite = $data['quantite'] ?? 1; // Le nombre d'articles ajoutés

        if (!$produitId) {
            return new JsonResponse(['error' => 'Produit manquant'], 400);
        }

        $produit = $produitRepository->find($produitId);
        if (!$produit) {
            return new JsonResponse(['error' => 'Produit introuvable'], 404);
        }

        // 3. Trouver le panier en cours (status = 'panier')
        $commandePanier = $commandeRepository->findOneBy([
            'User' => $user,
            'status' => 'panier'
        ]);

        // Si aucun panier n'existe, on le crée
        if (!$commandePanier) {
            $commandePanier = new Commande();
            $commandePanier->setUser($user);
            $commandePanier->setStatus('panier');
            $commandePanier->setCreatedAt(new \DateTime());
            $em->persist($commandePanier);
        }

        // 4. Parcourir les lignes existantes pour voir si le produit y est déjà
        $ligneExistante = null;
        foreach ($commandePanier->getLigneCommandes() as $ligne) {
            if ($ligne->getProduit()->getId() === $produit->getId()) {
                $ligneExistante = $ligne;
                break;
            }
        }

        if ($ligneExistante) {
            // Le produit est déjà là, on incrémente avec ton champ $count
            $ligneExistante->setCount($ligneExistante->getCount() + $quantite);
        } else {
            // Nouveau produit : on crée une LigneCommande avec ton champ $count
            $ligneCommande = new LigneCommande();
            $ligneCommande->setCommande($commandePanier);
            $ligneCommande->setProduit($produit);
            $ligneCommande->setCount($quantite); // <-- CORRIGÉ ICI
            $em->persist($ligneCommande);
        }

        // 5. Enregistrement en BDD
        $em->flush();

        return new JsonResponse(['status' => 'Produit ajouté au panier avec succès !'], 200);
    }
}