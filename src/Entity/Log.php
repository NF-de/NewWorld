<?php

namespace App\Entity;

use App\Repository\LogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LogRepository::class)]
class Log
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'logs')]
    private ?entreprise $entreprise_id = null;

    #[ORM\Column(length: 255)]
    private ?string $operation = null;


    #[ORM\Column(length: 255)]
    private ?string $message = null;

    #[ORM\Column]
    private ?\DateTime $created_at = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $table_concernee = null;

    #[ORM\ManyToOne(inversedBy: 'logs')]
    private ?user $user_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntrepriseId(): ?entreprise
    {
        return $this->entreprise_id;
    }

    public function setEntrepriseId(?entreprise $entreprise_id): static
    {
        $this->entreprise_id = $entreprise_id;

        return $this;
    }

    public function getOperation(): ?string
    {
        return $this->operation;
    }

    public function setOperation(string $operation): static
    {
        $this->operation = $operation;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTime $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getTableConcernee(): ?string
    {
        return $this->table_concernee;
    }

    public function setTableConcernee(?string $table_concernee): static
    {
        $this->table_concernee = $table_concernee;

        return $this;
    }

    public function getUserId(): ?user
    {
        return $this->user_id;
    }

    public function setUserId(?user $user_id): static
    {
        $this->user_id = $user_id;

        return $this;
    }
}
