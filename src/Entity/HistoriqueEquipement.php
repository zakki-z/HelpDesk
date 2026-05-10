<?php

namespace App\Entity;

use App\Repository\HistoriqueEquipementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueEquipementRepository::class)]
class HistoriqueEquipement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_modification = null;

    #[ORM\Column(length: 255)]
    private ?string $champ_modifie = null;

    #[ORM\Column(length: 255)]
    private ?string $ancienne_valeur = null;

    #[ORM\Column(length: 255)]
    private ?string $nouvelle_valeur = null;

    #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: 'historiques')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipement $equipement = null;

    #[ORM\ManyToOne(targetEntity: Panne::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Panne $panne = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateModification(): ?\DateTimeInterface
    {
        return $this->date_modification;
    }

    public function setDateModification(\DateTimeInterface $date_modification): static
    {
        $this->date_modification = $date_modification;
        return $this;
    }

    public function getChampModifie(): ?string
    {
        return $this->champ_modifie;
    }

    public function setChampModifie(string $champ_modifie): static
    {
        $this->champ_modifie = $champ_modifie;
        return $this;
    }

    public function getAncienneValeur(): ?string
    {
        return $this->ancienne_valeur;
    }

    public function setAncienneValeur(string $ancienne_valeur): static
    {
        $this->ancienne_valeur = $ancienne_valeur;
        return $this;
    }

    public function getNouvelleValeur(): ?string
    {
        return $this->nouvelle_valeur;
    }

    public function setNouvelleValeur(string $nouvelle_valeur): static
    {
        $this->nouvelle_valeur = $nouvelle_valeur;
        return $this;
    }

    public function getEquipement(): ?Equipement
    {
        return $this->equipement;
    }

    public function setEquipement(?Equipement $equipement): static
    {
        $this->equipement = $equipement;
        return $this;
    }

    public function getPanne(): ?Panne
    {
        return $this->panne;
    }

    public function setPanne(?Panne $panne): static
    {
        $this->panne = $panne;
        return $this;
    }
}
