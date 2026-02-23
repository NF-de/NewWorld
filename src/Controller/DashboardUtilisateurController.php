<?php

namespace App\Controller;


use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;



final class DashboardUtilisateurController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $entreprise = $entityManager->getRepository(Entreprise::class)->findOneBy(['user' => $user]);

        return $this->render('dashboard_utilisateur/index.html.twig', [

            'entreprise' => $entreprise,
            'user' => $user,
        ]);

    }

    #[Route('/dashboard/update-user', name: 'app_dashboard_update_user', methods: ['POST'])]
    public function updateUser(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $user->setNom($request->request->get('nom'));
        $user->setPrenom($request->request->get('prenom'));
        $user->setEmail($request->request->get('email'));

        $em->flush();

        $this->addFlash('success', 'Vos informations utilisateur ont été mises à jour !');
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/dashboard/update-entreprise', name: 'app_dashboard_update_entreprise', methods: ['POST'])]
    public function updateEntreprise(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $entreprise = $em->getRepository(Entreprise::class)->findOneBy(['user' => $user]);

        if ($entreprise) {
            $entreprise->setNom($request->request->get('nom'));
            $entreprise->setEmail($request->request->get('email'));
            $entreprise->setTelephone($request->request->get('telephone'));
            $entreprise->setAdresse($request->request->get('adresse'));
            $entreprise->setVille($request->request->get('ville'));
            $entreprise->setCodePostal($request->request->get('codePostal'));
            $entreprise->setSiret($request->request->get('siret'));

            $em->flush();
            $this->addFlash('success', 'Les informations de l’entreprise ont été mises à jour !');
        }

        return $this->redirectToRoute('app_dashboard');
    }

}
