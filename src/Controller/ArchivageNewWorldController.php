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

        if (!$id) {
            $this->addFlash("error", "Aucun id");
        }

        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $entreprises = $em->getRepository(Entreprise::class)->findAll();
        foreach ($entreprises as $entreprise) {
            if ($entreprise->getId() == $id) {
                $entrepriseToUse = $entreprise;
            }
        }

        if (isset($entrepriseToUse)) {


            $currentTime = new DateTime();

            $dateValidation = $entrepriseToUse->getDateValidation();

            $timeEndContractActualYear = (clone $dateValidation)->setDate(
                (int) date('Y'),
                (int) $dateValidation->format('m'),
                (int) $dateValidation->format('d')
            );
            $timeEndContract = (clone $timeEndContractActualYear)->modify('+1 year');
            $timeToCompare = (clone $timeEndContract)->modify('-6 months');

            if ($currentTime < $timeToCompare) {
                if ($entrepriseToUse->getStatus() != "pre_avis_entreprise" || $entrepriseToUse->getStatus() != "archive" || $entrepriseToUse->getStatus() != "pre_avis_newworld") {
                    $entrepriseToUse->setStatus("pre_avis_newworld");
                    $entrepriseToUse->setDatePreAvis(new DateTime());
                    $dateFin = $timeEndContract;
                    $entrepriseToUse->setDateFin($dateFin);
                    $em->flush();
                } else {
                    $this->addFlash("error", "L'entreprise est déjà en pré avis");
                }
            } else {
                if ($entrepriseToUse->getStatus() != "pre_avis_entreprise" || $entrepriseToUse->getStatus() != "archive" || $entrepriseToUse->getStatus() != "pre_avis_newworld") {
                    $entrepriseToUse->setStatus("pre_avis_newworld");
                    $entrepriseToUse->setDatePreAvis(new DateTime());
                    $dateFin = $timeEndContract->modify("+1 year");
                    $entrepriseToUse->setDateFin($dateFin);
                    $em->flush();
                } else {
                    $this->addFlash("error", "L'entreprise est déjà en pré avis");
                }
                $this->addFlash("warning", "La date de préavis de l'anné actuelle est dépassé, le contrat durera jusqu'à l'anné suivante");
            }


        } else {
            $this->addFlash("error", "Aucune entreprise trouvé avec l'id fournit");
        }

        return $this->redirectToRoute('admin_entreprise_index');

    }
}
