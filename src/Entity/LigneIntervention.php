<?php

namespace App\Entity;

use App\Repository\LigneInterventionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LigneInterventionRepository::class)]
class LigneIntervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $quantite_utilisee = null;

    #[ORM\ManyToOne(targetEntity: Intervention::class, inversedBy: 'lignesIntervention')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Intervention $intervention = null;

    #[ORM\ManyToOne(targetEntity: Stock::class, inversedBy: 'lignesIntervention')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stock $stock = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantiteUtilisee(): ?int
    {
        return $this->quantite_utilisee;
    }

    public function setQuantiteUtilisee(int $quantite_utilisee): static
    {
        $this->quantite_utilisee = $quantite_utilisee;
        return $this;
    }

    public function getIntervention(): ?Intervention
    {
        return $this->intervention;
    }

    public function setIntervention(?Intervention $intervention): static
    {
        $this->intervention = $intervention;
        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->stock;
    }

    public function setStock(?Stock $stock): static
    {
        $this->stock = $stock;
        return $this;
    }
}
