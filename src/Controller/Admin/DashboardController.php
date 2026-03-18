<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use App\Entity\Entreprise;
use App\Entity\Log;
use Doctrine\ORM\EntityManagerInterface;

#[IsGranted('ROLE_SECRETAIRE')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private EntityManagerInterface $entityManager;

    // 1. On injecte l'EntityManager pour pouvoir faire des requêtes
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    public function index(): Response
    {
        // 2. On prépare les données
        $stats = [
            'users' => $this->entityManager->getRepository(User::class)->count([]),
            'entreprises' => $this->entityManager->getRepository(Entreprise::class)->count([]),
            'entreprises_valides' => $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'valide']),
        ];

        // 3. ON ENVOIE LA VARIABLE AU TEMPLATE
        return $this->render('admin/index.html.twig', [
            'stats' => $stats,
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('NewWorld Admin')
            ->setFaviconPath('assets/images/tractor.svg');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        yield MenuItem::linkTo(DemandeController::class, 'Demande partenaire', 'fas fa-envelope')->setAction('index');
        yield MenuItem::linkTo(PartenaireController::class, 'Partenaire', 'fas fa-list')->setAction('index');
        yield MenuItem::linkTo(ArchivageListController::class, 'Archivé', 'fas fa-file-zipper')->setAction('index');

        // Section Gestion
        yield MenuItem::section('Gestion Utilisateurs');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);

        // Section Administration (avec vérification de rôle)
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Administration système');

            // Un sous-menu pour regrouper Entreprises et Logs
            yield MenuItem::linkToCrud('Entreprises', 'fas fa-building', Entreprise::class);
            yield MenuItem::linkToCrud('Logs système', 'fas fa-file-alt', Log::class);


            yield MenuItem::section('Maintenance');
            yield MenuItem::linkToRoute('Vider le Cache', 'fas fa-broom', 'app_clear_cache');
        }

        // Section Liens Externes
        yield MenuItem::section(); // Ligne de séparation
        yield MenuItem::linkToLogout('Déconnexion', 'fas fa-sign-out-alt');
    }

}
