<?php

namespace App\Controller\Admin;

use App\Entity\Entreprise;
use App\Entity\Log;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class LogCrudController extends AbstractCrudController
{
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
            AssociationField::new('entreprise')
                ->setLabel("Entreprise")
                ->setFormTypeOption(
                    "choice_label",
                    function (Entreprise $entreprise) {
                        return $entreprise->getEmail();
                    }
                ),
            TextEditorField::new('message')
        ];
    }

}
