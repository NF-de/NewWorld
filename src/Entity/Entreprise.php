<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\EntrepriseRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EntrepriseRepository::class)]
#[ApiResource]
#[ORM\HasLifecycleCallbacks]
class Entreprise
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    
    private ?string $adresse = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read'])]
    private ?string $ville = null;

    #[ORM\Column(length: 5)]
    private ?string $code_postal = null;

    #[ORM\Column(length: 14)]
    private ?string $siret = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read'])]
    private ?string $status = null;

    #[ORM\Column(length: 255)]
    #[Groups(['produit:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 20)]
    #[Groups(['produit:read'])]
    private ?string $telephone = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_validation = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_archivage = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $date_mise_a_jour = null;

    #[ORM\OneToOne(inversedBy: 'entreprise', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $dateFin = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTime $datePreAvis = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $causeRefus = null;

    /**
     * @var Collection<int, Produit>
     */
    #[ORM\OneToMany(targetEntity: Produit::class, mappedBy: 'entreprise', orphanRemoval: true)]
    private Collection $produits;

    #[ORM\Column(nullable: true)]
    private ?bool $miseEnAvant = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $descriptif = null;

    public function __construct()
    {
        $this->produits = new ArrayCollection();
    }

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): static
    {
        $this->user = $user;

        return $this;
    }


    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->date_mise_a_jour = new \DateTime();
    }

    #[ORM\PreUpdate]
    public function onPreUpdate(): void
    {
        $this->date_mise_a_jour = new \DateTime();
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin): static
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    public function getDatePreAvis(): ?\DateTime
    {
        return $this->datePreAvis;
    }

    public function setDatePreAvis(?\DateTime $datePreAvis): static
    {
        $this->datePreAvis = $datePreAvis;

        return $this;
    }

    public function getCauseRefus(): ?string
    {
        return $this->causeRefus;
    }

    public function setCauseRefus(?string $causeRefus): static
    {
        $this->causeRefus = $causeRefus;

        return $this;
    }

    /**
     * @return Collection<int, Produit>
     */
    public function getProduits(): Collection
    {
        return $this->produits;
    }

    public function addProduit(Produit $produit): static
    {
        if (!$this->produits->contains($produit)) {
            $this->produits->add($produit);
            $produit->setEntreprise($this);
        }

        return $this;
    }

    public function removeProduit(Produit $produit): static
    {
        if ($this->produits->removeElement($produit)) {
            // set the owning side to null (unless already changed)
            if ($produit->getEntreprise() === $this) {
                $produit->setEntreprise(null);
            }
        }

        return $this;
    }

    public function isMiseEnAvant(): ?bool
    {
        return $this->miseEnAvant;
    }

    public function setMiseEnAvant(?bool $miseEnAvant): static
    {
        $this->miseEnAvant = $miseEnAvant;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(?string $descriptif): static
    {
        $this->descriptif = $descriptif;

        return $this;
    }

}
