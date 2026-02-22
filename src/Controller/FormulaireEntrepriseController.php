<?php

namespace App\Controller;

use App\Entity\Entreprise;
use App\Form\FormulaireEntrepriseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FormulaireEntrepriseController extends AbstractController
{

    #[Route('/formentreprise', name: 'app_formulaire_entreprise')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Création nouvelle entreprise
        $entreprise = new Entreprise();

        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Vérifier si l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Associer l'entreprise à l'utilisateur connecté
        $entreprise->setUser($user);

        // Vérifier si l'utilisateur a déjà une entreprise
        $existingEntreprise = $entityManager->getRepository(Entreprise::class)->findOneBy(['user' => $user]);

        // Si l'utilisateur a déjà une entreprise, 
        if ($existingEntreprise) {

            $this->addFlash('warning', 'Votre demande d\'entreprise a déjà été effectuée.');

            return $this->redirectToRoute('app_main');
        }

        // Créer le formulaire
        $form = $this->createForm(FormulaireEntrepriseType::class, $entreprise);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entreprise->setStatus('non_valide');

            // Enregistrer l'entreprise en base de données
            $entityManager->persist($entreprise);
            $entityManager->flush();

            $this->addFlash('success', 'Votre demande d\'entreprise a été soumise avec succès.');
            return $this->redirectToRoute('app_main');
        }

        return $this->render('formulaire_entreprise/index.html.twig', [
            'form' => $form->createView(),
        ]);


    }
}
