<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Entreprise;
use App\Entity\User; // Indispensable pour le typage
use DateTime;

final class ArchivageController extends AbstractController
{
    #[Route('/archivage', name: 'app_archivage')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if (!$user instanceof User) {
            return $this->redirectToRoute('app_login');
        }

        $entreprise = $user->getEntreprise();
        
        if ($entreprise) {
            if ($entreprise->getStatus() !== "pre_avis_entreprise" && $entreprise->getStatus() !== "archive") {
                $entreprise->setStatus("pre_avis_entreprise");
                $entreprise->setDatePreAvis(new DateTime());
                
                $dateFin = new DateTime();
                $dateFin->modify('+2 months');
                $entreprise->setDateFin($dateFin);
                
                $em->flush();
                $this->addFlash("success", "Votre entreprise a été mise en pré-avis d'archivage.");
            }
        } else {
            $this->addFlash("error", "Votre utilisateur a besoin d'avoir une entreprise");
        }

        return $this->redirectToRoute('app_dashboard');
    }
}