<?php

namespace App\Entity;

use App\Repository\MouvementStockRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MouvementStockRepository::class)]
class MouvementStock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $type = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_mouvement = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\ManyToOne(targetEntity: Stock::class, inversedBy: 'mouvements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stock $stock = null;

    #[ORM\ManyToOne(targetEntity: Personnel::class, inversedBy: 'mouvementsStock')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $effectuePar = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): static
    {
        $this->type = $type;
        return $this;
    }

    public function getDateMouvement(): ?\DateTimeInterface
    {
        return $this->date_mouvement;
    }

    public function setDateMouvement(\DateTimeInterface $date_mouvement): static
    {
        $this->date_mouvement = $date_mouvement;
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
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

    public function getEffectuePar(): ?Personnel
    {
        return $this->effectuePar;
    }

    public function setEffectuePar(?Personnel $effectuePar): static
    {
        $this->effectuePar = $effectuePar;
        return $this;
    }
}
