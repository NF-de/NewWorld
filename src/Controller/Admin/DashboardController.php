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
use Symfony\Component\ExpressionLanguage\Expression;
use Doctrine\ORM\EntityManagerInterface;

#[IsGranted(new Expression('is_granted("ROLE_SECRETAIRE") or is_granted("ROLE_DIRECTOR")'))]
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
            'entreprises_non_valides' => $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'non_valide']),
            'demande' => $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'attente']),
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
        $nombreEntreprisesPartenaire = $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'valide']);
        $nombreEntreprisesPartenaire = $nombreEntreprisesPartenaire + $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'pre_avis_newworld']);
        $nombreEntreprisesPartenaire = $nombreEntreprisesPartenaire + $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'pre_avis_entreprise']);
        $nombreEntreprisesAttente = $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'attente']);
        $nombreEntreprisesArchive = $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'archive']);
        $nombreEntreprisesAttenteQualite = $this->entityManager->getRepository(Entreprise::class)->count(['status' => 'attente_qualite']);

        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');
        if ($this->isGranted('ROLE_DIRECTOR')) {
            yield MenuItem::linkTo(DemandeController::class, 'Demande partenaire', 'fas fa-envelope')
                ->setAction('index')
                ->setBadge($nombreEntreprisesAttente, 'badge bg-primary');
        }
        if ($this->isGranted('ROLE_DIRECTOR')) {

            yield MenuItem::linkTo(PartenaireController::class, 'Partenaires', 'fas fa-list')
                ->setAction('index')
                ->setBadge($nombreEntreprisesPartenaire, 'badge bg-primary');
        }
        if ($this->isGranted('ROLE_DIRECTOR')) {

            yield MenuItem::linkTo(ArchivageListController::class, 'Archivé', 'fas fa-file-zipper')
                ->setAction('index')
                ->setBadge($nombreEntreprisesArchive, 'badge bg-primary');
        }
        yield MenuItem::linkTo(QualityController::class, 'Contrôle qualité', 'fas fa-broom')
            ->setAction('index')
            ->setBadge($nombreEntreprisesAttenteQualite, 'badge bg-primary');
        // Section Gestion
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Gestion Utilisateurs');
            yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        }
        // Section Administration (avec vérification de rôle)
        yield MenuItem::section('Administration système');

        // Ce lien mène à la LISTE (Index), pas à l'édition, donc il est correct
        yield MenuItem::linkToCrud('Entreprises', 'fas fa-building', Entreprise::class);

        // Vérification de rôle pour les logs
        if ($this->isGranted('ROLE_DIRECTOR')) {
            yield MenuItem::linkToCrud('Logs système', 'fas fa-file-alt', Log::class);
        }
        // Section Liens Externes
        yield MenuItem::section(); // Ligne de séparation
        yield MenuItem::linkToLogout('Déconnexion', 'fas fa-sign-out-alt');
    }

}
