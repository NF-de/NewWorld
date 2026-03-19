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


class LogCrudController extends AbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityPermission('ROLE_ADMIN');
    }

    public static function getEntityFqcn(): string
    {
        return Log::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('operation'),
            TextField::new('table_concernee'),
            TimeField::new('created_at'),
            AssociationField::new('user')
                ->setLabel("Utilisateur")
                ->setFormTypeOption(
                    "choice_label",
                    function (User $user) {
                        return $user->getEmail();
                    }
                ),
            TextEditorField::new('message')
        ];
    }

}
