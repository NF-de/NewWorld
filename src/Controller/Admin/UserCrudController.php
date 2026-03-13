<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
// class UserCrudController extends AbstractCrudController
// {
//     public static function getEntityFqcn(): string
//     {
//         return User::class;
//     }


//     public function configureFields(string $pageName): iterable
//     {
//         return [
//             TextField::new('nom'),
//             TextField::new('prenom'),
//             TextField::new('email'),
//             TextField::new('password')
//             ->setFormType(PasswordType::class)
//             ->onlyOnForms()

//         ];
//     }

// }
class UserCrudController extends AbstractCrudController
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->setPermission(Action::DELETE, 'ROLE_ADMIN');
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des utilisateurs')
            ->setPageTitle(Crud::PAGE_NEW, 'Créer un nouvel utilisateur')
            // Affiche le nom/prénom de l'utilisateur dynamiquement
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier l’utilisateur');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('email'),
            ChoiceField::new('roles')
                ->setLabel('Roles')
                ->setChoices([
                    'Admin' => 'ROLE_ADMIN',
                    'Entreprise' => 'ROLE_ENTREPRISE',
                    'Secrétaire' => 'ROLE_SECRETAIRE',
                    'Directeur' => 'ROLE_ADMIN',
                ])
                ->allowMultipleChoices(),


        ];
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Hash du mot de passe
            if ($entityInstance->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $entityInstance->getPassword()
                );
                $entityInstance->setPassword($hashedPassword);
            }
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            if ($entityInstance->getPassword()) {
                $hashedPassword = $this->passwordHasher->hashPassword(
                    $entityInstance,
                    $entityInstance->getPassword()
                );
                $entityInstance->setPassword($hashedPassword);
            }
        }

        parent::updateEntity($entityManager, $entityInstance);
    }
}