<?php

namespace App\Entity;

use App\Repository\StockRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockRepository::class)]
class Stock
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom_article = null;

    #[ORM\Column]
    private ?int $quantite = null;

    #[ORM\Column]
    private ?int $seuil_min = null;

    /**
     * @var Collection<int, MouvementStock>
     */
    #[ORM\OneToMany(targetEntity: MouvementStock::class, mappedBy: 'stock')]
    private Collection $mouvements;

    /**
     * @var Collection<int, LigneIntervention>
     */
    #[ORM\OneToMany(targetEntity: LigneIntervention::class, mappedBy: 'stock')]
    private Collection $lignesIntervention;

    public function __construct()
    {
        $this->mouvements = new ArrayCollection();
        $this->lignesIntervention = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomArticle(): ?string
    {
        return $this->nom_article;
    }

    public function setNomArticle(string $nom_article): static
    {
        $this->nom_article = $nom_article;
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

    public function getSeuilMin(): ?int
    {
        return $this->seuil_min;
    }

    public function setSeuilMin(int $seuil_min): static
    {
        $this->seuil_min = $seuil_min;
        return $this;
    }

    /**
     * @return Collection<int, MouvementStock>
     */
    public function getMouvements(): Collection
    {
        return $this->mouvements;
    }

    /**
     * @return Collection<int, LigneIntervention>
     */
    public function getLignesIntervention(): Collection
    {
        return $this->lignesIntervention;
    }
}
