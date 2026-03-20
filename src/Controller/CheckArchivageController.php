<?php

namespace App\Controller;

use App\Entity\Entreprise;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use DateTime;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class CheckArchivageController extends AbstractController
{
    //#[IsGranted('ROLE_ADMIN')]
    #[Route('/check_archivage', name: 'app_check_archivage')]
    public function index(EntityManagerInterface $em): Response
    {

        $user = $this->getUser();

        //Vérifie que l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        //Vérifie que l'utilisateur est admin ou directeur
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_DIRECTOR')) {
            return $this->redirectToRoute('app_login');
        }

        //Récupération de toutes les entreprises
        $entreprises = $em->getRepository(Entreprise::class)->findAll();
        if ($entreprises) {
            foreach ($entreprises as $entreprise) {
                $currentTime = new DateTime();
                $timeToCompare = $entreprise->getDateFin();

                if ($timeToCompare <= $currentTime && $entreprise->getStatus() == "pre_avis_entreprise") {
                    $entreprise->setStatus('archive');
                    $entreprise->setDateArchivage($currentTime);
                    $em->flush();
                }
            }
        }

        return $this->redirectToRoute('admin');
    }
}
