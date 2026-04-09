<?php

namespace App\Entity;

use App\Repository\LigneCommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneCommandeRepository::class)]
class LigneCommande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $count = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    private ?produit $produit_id = null;

    #[ORM\ManyToOne(inversedBy: 'ligneCommandes')]
    private ?commande $commande_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getProduitId(): ?produit
    {
        return $this->produit_id;
    }

    public function setProduitId(?produit $produit_id): static
    {
        $this->produit_id = $produit_id;

        return $this;
    }

    public function getCommandeId(): ?commande
    {
        return $this->commande_id;
    }

    public function setCommandeId(?commande $commande_id): static
    {
        $this->commande_id = $commande_id;

        return $this;
    }
}
