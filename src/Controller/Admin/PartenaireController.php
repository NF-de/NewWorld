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

final class PartenaireController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_EDITOR")'))]
    #[AdminRoute("/admin/partenaire", "admin_partenaire_index")]
    public function index(EntityManagerInterface $em): Response
    {
        $partenaires = $em->getRepository(Entreprise::class)->findAll();

        foreach ($partenaires as $partenaire) {
            if ($partenaire->getStatus() == "valide") {
                $partenairesValide[] = $partenaire;
            }
        }

        return $this->render('admin/partenaire/index.html.twig', [
            'partenaires' => $partenairesValide
        ]);
    }
}
