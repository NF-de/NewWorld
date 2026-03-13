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

#[IsGranted('ROLE_SECRETAIRE')]
#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'DashboardController',
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
        
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('User', 'fas fa-circle-user', User::class);

        // Seulement pour ADMIN
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::linkToCrud('Entreprise', 'fas fa-building', Entreprise::class);
            yield MenuItem::linkToCrud('Log', 'fas fa-file', Log::class);
        }
    }

}
