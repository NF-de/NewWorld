<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiUserController extends AbstractController
{
    #[Route('/api/user/update', name: 'api_user_update', methods: ['PUT'])]
    public function updateProfile(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ): JsonResponse {
        
        // 1. Récupérer l'utilisateur connecté via le Token JWT
        /** @var User $user */
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Utilisateur non authentifié'], 401);
        }

        // 2. Décoder les données reçues de Flutter
        $data = json_decode($request->getContent(), true);
        if (!$data) {
            return new JsonResponse(['error' => 'Données JSON invalides'], 400);
        }

        // 3. Appliquer les modifications si elles sont fournies
        if (isset($data['nom'])) {
            $user->setNom($data['nom']);
        }
        
        if (isset($data['prenom'])) {
            $user->setPrenom($data['prenom']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        // Si un nouveau mot de passe est soumis, on applique temporairement la version en clair
        // pour que le validateur puisse vérifier tes contraintes Regex et de longueur.
        $nouveauPassword = $data['password'] ?? null;
        if (!empty($nouveauPassword)) {
            $user->setPassword($nouveauPassword);
        }

        // 4. Validation des contraintes de l'entité (Email unique, force du mot de passe...)
        $errors = $validator->validate($user);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $messages], 400);
        }

        // 5. Si la validation passe et qu'un mot de passe a été fourni, on le hache enfin !
        if (!empty($nouveauPassword)) {
            $hashedPassword = $passwordHasher->hashPassword($user, $nouveauPassword);
            $user->setPassword($hashedPassword);
        }

        // 6. Sauvegarde des changements
        try {
            $entityManager->flush(); // Déclenche automatiquement onPreUpdate()
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Impossible de mettre à jour le profil (Email potentiellement déjà utilisé).'], 400);
        }

        return new JsonResponse(['status' => 'Profil mis à jour avec succès !'], 200);
    }
}