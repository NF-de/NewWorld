<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Entreprise;
use App\DataFixtures\UserFixture;
use App\Entity\User;

class EntrepriseFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $file = __DIR__ . '/Ressources/Entreprise.csv';

        if (!file_exists($file)) {
            throw new \Exception("CSV file not found: $file");
        }

        // Adapter le séparateur si besoin (ici ,)
        $rows = array_map('str_getcsv', file($file));
        $header = array_shift($rows);

        foreach ($rows as $row) {

            $data = array_combine($header, $row);

            $entreprise = new Entreprise();

            // 🔗 User récupéré via les références
            $entreprise->setUser(
                $this->getReference('user_' . $data['user_id'], User::class)
            );


            $entreprise->setNom($data['nom']);
            $entreprise->setAdresse($data['adresse']);
            $entreprise->setVille($data['ville']);
            $entreprise->setCodePostal($data['code_postal']);
            $entreprise->setSiret($data['siret']);
            $entreprise->setStatus($data['status']);
            $entreprise->setEmail($data['email']);
            $entreprise->setTelephone($data['telephone']);

            // Dates sécurisées
            $entreprise->setDateValidation(
                !empty($data['date_validation']) ? new \DateTime($data['date_validation']) : null
            );

            $entreprise->setDateArchivage(
                !empty($data['date_archivage']) ? new \DateTime($data['date_archivage']) : new \DateTime()
            );


            $entreprise->setDateMiseAJour(
                !empty($data['date_mise_a_jour']) ? new \DateTime($data['date_mise_a_jour']) : new \DateTime()
            );

            $manager->persist($entreprise);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            UserFixture::class
        ];
    }
}
