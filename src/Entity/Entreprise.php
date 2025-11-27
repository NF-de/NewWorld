<?php

namespace App\Entity;

use App\Repository\EntrepriseRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    private ?string $ville = null;

    #[ORM\Column(length: 255)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 14)]
    private ?string $siret = null;

    #[ORM\Column(length: 20)]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\Column(length: 20)]
    private ?string $telephone = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_validation = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_archivage = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $date_mise_a_jour = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): static
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->code_postal;
    }

    public function setCodePostal(string $code_postal): static
    {
        $this->code_postal = $code_postal;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): static
    {
        $this->siret = $siret;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getDateValidation(): ?\DateTime
    {
        return $this->date_validation;
    }

    public function setDateValidation(\DateTime $date_validation): static
    {
        $this->date_validation = $date_validation;

        return $this;
    }

    public function getDateArchivage(): ?\DateTime
    {
        return $this->date_archivage;
    }

    public function setDateArchivage(\DateTime $date_archivage): static
    {
        $this->date_archivage = $date_archivage;

        return $this;
    }

    public function getDateMiseAJour(): ?\DateTime
    {
        return $this->date_mise_a_jour;
    }

    public function setDateMiseAJour(\DateTime $date_mise_a_jour): static
    {
        $this->date_mise_a_jour = $date_mise_a_jour;

        return $this;
    }
}
