<?php

namespace App\Controller\Admin;

use App\Entity\Entreprise;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class EntrepriseCrudController extends AbstractCrudController
{


    public static function getEntityFqcn(): string
    {
        return Entreprise::class;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            // La sécurité
            ->setEntityPermission('ROLE_ADMIN')

            // Les titres personnalisés
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier les informations de l’entreprise')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une nouvelle entreprise')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des entreprises');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            //IdField::new('id'),
            TextField::new('nom')->hideOnIndex(),
            TextField::new('adresse')->hideOnIndex(),
            TextField::new('ville')->hideOnIndex(),
            IntegerField::new('code_postal'),
            IntegerField::new('siret'),
            TextField::new('email')->setLabel("Email de l'entreprise"),
            IntegerField::new('telephone'),
            //TextEditorField::new('email'),
            AssociationField::new('user')
                ->setLabel("Gérant")
                ->setFormTypeOption(
                    "choice_label",
                    function (User $user) {
                        return $user->getEmail();
                    }
                ),
            TextField::new('status')
                ->setLabel('Statut')
                ->formatValue(function ($value) {
                    return match ($value) {
                        'valide' => '<span class="badge text-bg-success">Validé</span>',
                        'pre_avis_entreprise' => '<span class="badge text-bg-warning">Pré avis</span>',
                        'pre_avis_newworld' => '<span class="badge text-bg-warning">Pré avis</span>',
                        'archive' => '<span class="badge text-bg-secondary">Archivé</span>',
                        'attente' => '<span class="badge text-bg-secondary">En attente</span>',
                        'non_valide' => '<span class="badge text-bg-danger">Refusé</span>',
                        default => '<span class="badge text-bg-secondary">Inconnu</span>',
                    };
                })
                ->renderAsHtml(),

        ];
    }

    public function configureActions(Actions $actions): Actions
    {
        $actionPreavis = Action::new('faire_preavis', 'Lancer le Préavis', 'fa fa-bell')
            ->linkToRoute('app_archivage_newworld', function ($entity) {
                return ['id' => $entity->getId()];
            })

            ->displayIf(static function ($entity) {
                $dateValidation = $entity->getDateValidation();

                // Si pas de date, on n'affiche rien
                if (!$dateValidation) {
                    return false;
                }

                $aujourdhui = new \DateTimeImmutable('today');
                $anneeEnCours = (int) $aujourdhui->format('Y');

                // On crée deux fenêtres de tir : 
                // 1. Celle de l'année dernière (ex: 01/12/2025 -> 01/06/2026)
                // 2. Celle de cette année (ex: 01/12/2026 -> 01/06/2027)
    
                // Fenêtre 1 (Année précédente)
                $debut1 = (clone $dateValidation)->setDate($anneeEnCours - 1, (int) $dateValidation->format('m'), (int) $dateValidation->format('d'));
                $fin1 = (clone $debut1)->modify('+6 months');

                // Fenêtre 2 (Année en cours)
                $debut2 = (clone $dateValidation)->setDate($anneeEnCours, (int) $dateValidation->format('m'), (int) $dateValidation->format('d'));
                $fin2 = (clone $debut2)->modify('+6 months');

                // Vérification
                $estDansFenetre1 = ($aujourdhui >= $debut1 && $aujourdhui <= $fin1);
                $estDansFenetre2 = ($aujourdhui >= $debut2 && $aujourdhui <= $fin2);

                return $estDansFenetre1 || $estDansFenetre2;
            });
        $valider = Action::new('valider', 'Valider', 'fa fa-check')
            ->linkToCrudAction('changeStatusToValide')
            ->displayIf(static function ($entity) {
                return $entity->getStatus() === 'attente';
            });
        return $actions
            ->add(Crud::PAGE_INDEX, $actionPreavis)
            ->add(Crud::PAGE_DETAIL, $actionPreavis)

            ->add(Crud::PAGE_INDEX, $valider)
            ->add(Crud::PAGE_DETAIL, $valider);
    }

    public function changeStatusToValide(AdminContext $context, AdminUrlGenerator $adminUrlGenerator)
    {
        $entreprise = $context->getEntity()->getInstance();

        // On change le statut
        $entreprise->setStatus('valide');

        // On sauvegarde
        $this->container->get('doctrine')->getManager()->flush();

        // Notification flash
        $this->addFlash('success', 'Statut mis à jour : Validé');

        // On recharge la page
        return $this->redirect($adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->generateUrl());
    }
}
