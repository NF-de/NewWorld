<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;
use DateTime;

final class ArchivageNewWorldController extends AbstractController
{
    #[Route('/archivageNewWorld/{id}', name: 'app_archivage_newworld')]
    public function index(int $id, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $entreprises = $em->getRepository(Entreprise::class);
        foreach ($entreprises as $entreprise) {
            if ($entreprise->getId() == $id) {
                $entrepriseToUse = $id;
            }
        }

        if (isset($entrepriseToUse)) {


            $currentTime = new DateTime();
            $timeToCompare = $entreprise->getDateValidation()->modify('+6 months');

            if ($currentTime < $timeToCompare) {
                if ($entreprise->getStatus() != "pre_avis_entreprise" || $entreprise->getStatus() != "archive" || $entreprise->getStatus() != "pre_avis_newworld") {
                    $entreprise->setStatus("pre_avis_newworld");
                    $entreprise->setDateArchivage(new DateTime());
                    $em->flush();
                }
            }


        } else {
            $this->addFlash("error", "Aucune entreprise trouvé avec l'id fournit");
        }

        return $this->redirectToRoute('app_dashboard');

    }
}
