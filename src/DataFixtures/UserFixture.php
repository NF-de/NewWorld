<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/User.csv'; // Chemin vers ton CSV
        if (!file_exists($file)) {
            throw new \Exception("CSV file not found: $file");
        }

        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows); // enlève l'entête

        foreach ($rows as $row) {
            $data = array_combine($header, $row);

            $user = new User();
            $user->setEmail($data['email']);
            $user->setRoles([$data['roles'] ?? 'ROLE_USER']);
            $user->setNom($data['nom']);

            // Hashage du mot de passe
            $user->setPassword(
                $this->passwordHasher->hashPassword($user, $data['password'])
            );

            // Dates
            $user->setCreatedAt(new \DateTime($data['created_at']));
            $user->setUpdatedAt(new \DateTime($data['updated_at']));

            // last_login nullable
            $user->setLastLogin($data['last_login'] ? new \DateTime($data['last_login']) : null);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
