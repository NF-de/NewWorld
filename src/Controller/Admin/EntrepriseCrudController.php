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
            TextField::new('nom'),
            TextField::new('adresse'),
            TextField::new('ville'),
            IntegerField::new('code_postal'),
            IntegerField::new('siret'),
            ChoiceField::new('status')
                ->setLabel('Status')
                ->setChoices([
                    'Non Validé' => 'non_valide',
                    'Validé' => 'valide',
                    'Pré avis' => 'pre_avis',
                    'Archivé' => 'archive',
                ]),
            TextField::new('email'),
            IntegerField::new('telephone'),
            //TextEditorField::new('email'),
            AssociationField::new('user')
                ->setLabel("Utilisateur")
                ->setFormTypeOption(
                    "choice_label",
                    function (User $user) {
                        return $user->getEmail();
                    }
                )
        ];
    }

}
