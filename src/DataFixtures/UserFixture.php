<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Adresse;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\DataFixtures\AdresseFixture;

class UserFixture extends Fixture implements DependentFixtureInterface
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/User.csv';

        if (!file_exists($file)) {
            throw new \Exception("Fichier CSV introuvable : $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $user = new User();
            $user->setEmail($data['email']);
            $user->setNom($data['nom'] ?? null);
            $user->setPrenom($data['prenom'] ?? null);
            
            // On transforme la chaîne "ROLE_ADMIN,ROLE_USER" en tableau
            $user->setRoles(explode(',', $data['roles']));

            // Hashage sécurisé du mot de passe
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $data['password'])
            );

            // Gestion du last_login (nullable)
            if (!empty($data['last_login'])) {
                $user->setLastLogin(new \DateTime($data['last_login']));
            }

            // Liaison OneToOne avec l'Adresse
            // Note : Votre entité utilise le setter setAdresseId()
            if (!empty($data['adresse_id'])) {
                $user->setAdresseId(
                    $this->getReference('adresse_' . $data['adresse_id'], Adresse::class)
                );
            }

            // Note : created_at et updated_at sont gérés automatiquement 
            // par vos LifecycleCallbacks (onPrePersist) dans l'entité.

            $manager->persist($user);

            // Création de la référence pour EntrepriseFixture ou CommandeFixture
            $this->addReference('user_' . $data['id'], $user);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            AdresseFixture::class,
        ];
    }
}