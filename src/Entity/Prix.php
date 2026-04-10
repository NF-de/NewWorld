<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PrixRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PrixRepository::class)]
#[ApiResource]
class Prix
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'prix')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?float $valeurHT = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['produit:read'])]
    private ?float $valeurTTC = null;

    #[ORM\Column]
    #[Groups(['produit:read'])]
    private ?float $valeur_tva = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(?Produit $produit): static
    {
        $this->produit = $produit;

        return $this;
    }

    public function getValeurHT(): ?float
    {
        return $this->valeurHT;
    }

    public function setValeurHT(float $valeurHT): static
    {
        $this->valeurHT = $valeurHT;

        return $this;
    }

    public function getValeurTTC(): ?float
    {
        return $this->valeurTTC;
    }

    public function setValeurTTC(?float $valeurTTC): static
    {
        $this->valeurTTC = $valeurTTC;

        return $this;
    }

    public function getValeurTva(): ?float
    {
        return $this->valeur_tva;
    }

    public function setValeurTva(float $valeur_tva): static
    {
        $this->valeur_tva = $valeur_tva;

        return $this;
    }

}
