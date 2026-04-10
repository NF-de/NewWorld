<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class ApiRegisterController extends AbstractController
{
    #[Route('/api/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request, 
        UserPasswordHasherInterface $passwordHasher, 
        EntityManagerInterface $entityManager
    ): JsonResponse {
        // 1. Récupérer les données JSON envoyées par Flutter
        $data = json_decode($request->getContent(), true);

        // Vérification basique des champs
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Données incomplètes'], 400);
        }

        // 2. Création de l'utilisateur
        $user = new User();
        $user->setEmail($data['email']);
        $user->setNom($data['nom'] ?? ''); // Optionnel selon ton entité
        $user->setPrenom($data['prenom'] ?? '');

        // 3. Hachage du mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        // 4. Attribution du ROLE_CLIENT (C'est ici que la magie opère !)
        $user->setRoles(['ROLE_CLIENT']);

        // 5. Sauvegarde en base de données
        try {
            $entityManager->persist($user);
            $entityManager->flush();
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Email déjà utilisé ou erreur serveur'], 400);
        }

        return new JsonResponse(['status' => 'Utilisateur créé avec succès !'], 201);
    }
}