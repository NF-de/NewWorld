<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Entreprise;
use App\Entity\User;

final class ArchivageController extends AbstractController
{
    #[Route('/archivage', name: 'app_archivage')]
    public function index(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $entreprise = $user->getEntreprise();
        if ($entreprise) {
            if ($entreprise->getStatus() != "pre_avis" || $entreprise->getStatus() != "archive") {
                $entreprise->setStatus("pre_avis");
                $entreprise->setDateArchivage(new \DateTime());
                $em->flush();
            }
        } else {
            $this->addFlash("error", "Vôtre utilisateur a besoin d'avoir une entreprise");
        }


        return $this->redirectToRoute('app_dashboard');
    }
}
