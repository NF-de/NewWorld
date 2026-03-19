<?php

namespace App\Controller\Admin;

use App\Entity\Entreprise;
use App\Entity\Log;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;


class LogCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_DIRECTOR');
    }

    public static function getEntityFqcn(): string
    {
        return Log::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::EDIT)
            ->disable(Action::DELETE)
            ->disable(Action::NEW);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('operation')
                ->hideOnForm(),
            TextField::new('table_concernee')
                ->hideOnForm(),
            TimeField::new('created_at')
                ->hideOnForm(),
            AssociationField::new('user')
                ->setLabel("Utilisateur")
                ->setFormTypeOption(
                    "choice_label",
                    function (User $user) {
                        return $user->getEmail();
                    }
                )
                ->hideOnForm(),
            TextEditorField::new('message')
                ->hideOnForm(),
        ];
    }



}
