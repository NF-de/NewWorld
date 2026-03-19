<?php

namespace App\Controller\Admin;

use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\ExpressionLanguage\Expression;
use App\Entity\Entreprise;
use Doctrine\ORM\EntityManagerInterface;

final class QualityController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SECRETAIRE")'))]
    #[AdminRoute("/admin/quality", "quality_index")]
    public function index(EntityManagerInterface $em): Response
    {
        $partenaires = $em->getRepository(Entreprise::class)->findAll();
        $partenairesValide = [];

        foreach ($partenaires as $partenaire) {
            if ($partenaire->getStatus() == "attente_qualite") {
                $partenairesValide[] = $partenaire;
            }
        }

        return $this->render('admin/qualite/index.html.twig', [
            'partenaires' => $partenairesValide
        ]);
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SECRETAIRE")'))]
    #[AdminRoute("/quality/accept/{id}", "admin_quality_accept")]
    public function acceptDemande(EntityManagerInterface $em, int $id): Response
    {
        $partenaire = $em->getRepository(Entreprise::class)->find($id);

        if ($partenaire) {
            $partenaire->setStatus("valide");
            $partenaire->setDateValidation(new DateTime());
            $em->flush();
        } else {
            $this->addFlash("error", "Aucun partenaire trouvé veuillez rafraichir la page et réessayer");
        }

        return $this->redirectToRoute("admin_quality_index");
    }

    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_SECRETAIRE")'))]
    #[AdminRoute("/quality/deny/{id}", "admin_quality_deny")]
    public function denyDemande(EntityManagerInterface $em, int $id): Response
    {
        $partenaire = $em->getRepository(Entreprise::class)->find($id);

        if ($partenaire) {
            $partenaire->setStatus("non_valide");
            $em->flush();
        } else {
            $this->addFlash("error", "Aucun partenaire trouvé veuillez rafraichir la page et réessayer");
        }

        return $this->redirectToRoute("admin_quality_index");
    }
}
