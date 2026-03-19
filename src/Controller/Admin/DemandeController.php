<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;

final class DemandeController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_DIRECTOR")'))]
    #[AdminRoute("/admin/demande", "demande_index")]
    public function index(EntityManagerInterface $em): Response
    {
        $partenaires = $em->getRepository(Entreprise::class)->findAll();
        $partenairesDemande = [];

        foreach ($partenaires as $partenaire) {
            if ($partenaire->getStatus() == "attente") {
                $partenairesDemande[] = $partenaire;
            }
        }

        return $this->render('admin/demande/index.html.twig', [
            'partenaires' => $partenairesDemande
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_DIRECTOR")'))]
    #[AdminRoute("/demande/accept/{id}", "admin_demande_accept")]
    public function acceptDemande(EntityManagerInterface $em, int $id): Response
    {
        $partenaire = $em->getRepository(Entreprise::class)->find($id);

        if ($partenaire) {
            $partenaire->setStatus("attente_qualite");
            $em->flush();
        } else {
            $this->addFlash("error", "Aucun partenaire trouvé veuillez rafraichir la page et réessayer");
        }

        return $this->redirectToRoute("admin_demande_index");
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_DIRECTOR")'))]
    #[AdminRoute("/demande/deny/{id}", "admin_demande_deny")]
    public function denyDemande(EntityManagerInterface $em, int $id): Response
    {
        $partenaire = $em->getRepository(Entreprise::class)->find($id);

        if ($partenaire) {
            $partenaire->setStatus("non_valide");
            $partenaire->setCauseRefus("demande");
            $em->flush();
        } else {
            $this->addFlash("error", "Aucun partenaire trouvé veuillez rafraichir la page et réessayer");
        }

        return $this->redirectToRoute("admin_demande_index");
    }
}
