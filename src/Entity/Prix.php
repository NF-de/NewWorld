<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Repository\PrixRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Groups(['produit:read'])]
    private ?string $valeurHT = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: true)]
    #[Assert\Positive]
    #[Groups(['produit:read'])]
    private ?string $valeurTTC = null;

    #[ORM\Column(type: 'decimal', precision: 5, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 100)]
    #[Groups(['produit:read'])]
    private ?string $valeur_tva = null;

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

        if ($produit !== null && !$produit->getPrix()->contains($this)) {
            $produit->addPrix($this);
        }
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
