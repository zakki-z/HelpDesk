<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(length: 255)]
    private ?string $statut = null;

    #[ORM\Column(length: 255)]
    private ?string $priorite = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $description = null;

    // crée (Personnel 1,1 -- 0,N Ticket)
    #[ORM\ManyToOne(targetEntity: Personnel::class, inversedBy: 'ticketsCrees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $creePar = null;

    // modifié_par (Personnel 0,N -- 0,N Ticket)
    #[ORM\ManyToMany(targetEntity: Personnel::class, inversedBy: 'ticketsModifies')]
    #[ORM\JoinTable(name: 'ticket_modifie_par')]
    private Collection $modifiePar;

    // traitée_par (Responsable 0,N -- 1,1 Ticket)
    #[ORM\ManyToOne(targetEntity: Responsable::class, inversedBy: 'ticketsTraites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Responsable $traiteePar = null;

    // concerne (Ticket 0,N -- 1,1 Panne)
    #[ORM\ManyToOne(targetEntity: Panne::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panne $panne = null;

    // respecte (Ticket 0,N -- 1,1 SLA)
    #[ORM\ManyToOne(targetEntity: SLA::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SLA $sla = null;

    // concerne (Ticket 0,N -- 1,1 Equipement)
    #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Equipement $equipement = null;

    /**
     * @var Collection<int, HistoriqueTicket>
     */
    #[ORM\OneToMany(targetEntity: HistoriqueTicket::class, mappedBy: 'ticket')]
    private Collection $historiquesTicket;

    /**
     * @var Collection<int, Intervention>
     */
    #[ORM\OneToMany(targetEntity: Intervention::class, mappedBy: 'ticket')]
    private Collection $interventions;

    public function __construct()
    {
        $this->modifiePar = new ArrayCollection();
        $this->historiquesTicket = new ArrayCollection();
        $this->interventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->date_creation;
    }

    public function setDateCreation(\DateTimeInterface $date_creation): static
    {
        $this->date_creation = $date_creation;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getPriorite(): ?string
    {
        return $this->priorite;
    }

    public function setPriorite(string $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCreePar(): ?Personnel
    {
        return $this->creePar;
    }

    public function setCreePar(?Personnel $creePar): static
    {
        $this->creePar = $creePar;
        return $this;
    }

    /**
     * @return Collection<int, Personnel>
     */
    public function getModifiePar(): Collection
    {
        return $this->modifiePar;
    }

    public function addModifiePar(Personnel $personnel): static
    {
        if (!$this->modifiePar->contains($personnel)) {
            $this->modifiePar->add($personnel);
        }
        return $this;
    }

    public function removeModifiePar(Personnel $personnel): static
    {
        $this->modifiePar->removeElement($personnel);
        return $this;
    }

    public function getTraiteePar(): ?Responsable
    {
        return $this->traiteePar;
    }

    public function setTraiteePar(?Responsable $traiteePar): static
    {
        $this->traiteePar = $traiteePar;
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

    public function getSla(): ?SLA
    {
        return $this->sla;
    }

    public function setSla(?SLA $sla): static
    {
        $this->sla = $sla;
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

    /**
     * @return Collection<int, HistoriqueTicket>
     */
    public function getHistoriquesTicket(): Collection
    {
        return $this->historiquesTicket;
    }

    /**
     * @return Collection<int, Intervention>
     */
    public function getInterventions(): Collection
    {
        return $this->interventions;
    }
}
