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

final class ArchivageListController extends AbstractController
{
    #[IsGranted(new Expression('is_granted("ROLE_ADMIN") or is_granted("ROLE_EDITOR")'))]
    #[AdminRoute("/admin/archive", "admin_archive_index")]
    public function index(EntityManagerInterface $em): Response
    {
        $partenaires = $em->getRepository(Entreprise::class)->findAll();
        $partenairesArchive = [];

        foreach ($partenaires as $partenaire) {
            if ($partenaire->getStatus() == "archive") {
                $partenairesArchive[] = $partenaire;
            }
        }

        return $this->render('admin/archive/index.html.twig', [
            'partenaires' => $partenairesArchive
        ]);
    }
}
