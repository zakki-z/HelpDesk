<?php

namespace App\Entity;

use App\Repository\InterventionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InterventionRepository::class)]
class Intervention
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_debut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fin = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $commentaire = null;

    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ticket $ticket = null;

    #[ORM\ManyToOne(targetEntity: Technicien::class, inversedBy: 'interventions')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Technicien $technicien = null;

    /**
     * @var Collection<int, LigneIntervention>
     */
    #[ORM\OneToMany(targetEntity: LigneIntervention::class, mappedBy: 'intervention')]
    private Collection $lignesIntervention;

    public function __construct()
    {
        $this->lignesIntervention = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): static
    {
        $this->date_debut = $date_debut;
        return $this;
    }

    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(?\DateTimeInterface $date_fin): static
    {
        $this->date_fin = $date_fin;
        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): static
    {
        $this->commentaire = $commentaire;
        return $this;
    }

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;
        return $this;
    }

    public function getTechnicien(): ?Technicien
    {
        return $this->technicien;
    }

    public function setTechnicien(?Technicien $technicien): static
    {
        $this->technicien = $technicien;
        return $this;
    }

    /**
     * @return Collection<int, LigneIntervention>
     */
    public function getLignesIntervention(): Collection
    {
        return $this->lignesIntervention;
    }

    public function addLigneIntervention(LigneIntervention $ligne): static
    {
        if (!$this->lignesIntervention->contains($ligne)) {
            $this->lignesIntervention->add($ligne);
            $ligne->setIntervention($this);
        }
        return $this;
    }

    public function removeLigneIntervention(LigneIntervention $ligne): static
    {
        if ($this->lignesIntervention->removeElement($ligne)) {
            if ($ligne->getIntervention() === $this) {
                $ligne->setIntervention(null);
            }
        }
        return $this;
    }
}
