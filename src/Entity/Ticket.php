<?php

namespace App\Entity;

use App\Repository\TicketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
class Ticket
{
    public const STATUTS = [
        'ouvert'      => 'Ouvert',
        'assigne'     => 'Assigné',
        'en_cours'    => 'En cours',
        'resolu'      => 'Résolu',
        'ferme'       => 'Fermé',
    ];

    public const PRIORITES = [
        'faible'    => 'Faible',
        'normale'   => 'Normale',
        'haute'     => 'Haute',
        'critique'  => 'Critique',
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_creation = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_resolution = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_fermeture = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'Le statut est obligatoire.')]
    private ?string $statut = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: 'La priorité est obligatoire.')]
    #[Assert\Choice(choices: ['faible', 'normale', 'haute', 'critique'], message: 'Priorité invalide.')]
    private ?string $priorite = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'La description est obligatoire.')]
    #[Assert\Length(min: 10, minMessage: 'La description doit contenir au moins {{ limit }} caractères.')]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Personnel::class, inversedBy: 'ticketsCrees')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Personnel $creePar = null;

    #[ORM\ManyToMany(targetEntity: Personnel::class, inversedBy: 'ticketsModifies')]
    #[ORM\JoinTable(name: 'ticket_modifie_par')]
    private Collection $modifiePar;

    #[ORM\ManyToOne(targetEntity: Responsable::class, inversedBy: 'ticketsTraites')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Responsable $traiteePar = null;

    #[ORM\ManyToOne(targetEntity: Panne::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Panne $panne = null;

    #[ORM\ManyToOne(targetEntity: SLA::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?SLA $sla = null;

    #[ORM\ManyToOne(targetEntity: Equipement::class, inversedBy: 'tickets')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Equipement $equipement = null;

    #[ORM\OneToMany(targetEntity: HistoriqueTicket::class, mappedBy: 'ticket', cascade: ['remove'])]
    private Collection $historiquesTicket;

    #[ORM\OneToMany(targetEntity: Intervention::class, mappedBy: 'ticket', cascade: ['remove'])]
    private Collection $interventions;

    public function __construct()
    {
        $this->modifiePar        = new ArrayCollection();
        $this->historiquesTicket = new ArrayCollection();
        $this->interventions     = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->date_creation; }
    public function setDateCreation(\DateTimeInterface $d): static { $this->date_creation = $d; return $this; }

    public function getDateResolution(): ?\DateTimeInterface { return $this->date_resolution; }
    public function setDateResolution(?\DateTimeInterface $d): static { $this->date_resolution = $d; return $this; }

    public function getDateFermeture(): ?\DateTimeInterface { return $this->date_fermeture; }
    public function setDateFermeture(?\DateTimeInterface $d): static { $this->date_fermeture = $d; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $s): static { $this->statut = $s; return $this; }

    public function getPriorite(): ?string { return $this->priorite; }
    public function setPriorite(string $p): static { $this->priorite = $p; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $d): static { $this->description = $d; return $this; }

    public function getCreePar(): ?Personnel { return $this->creePar; }
    public function setCreePar(?Personnel $u): static { $this->creePar = $u; return $this; }

    public function getModifiePar(): Collection { return $this->modifiePar; }
    public function addModifiePar(Personnel $p): static
    {
        if (!$this->modifiePar->contains($p)) { $this->modifiePar->add($p); }
        return $this;
    }
    public function removeModifiePar(Personnel $p): static { $this->modifiePar->removeElement($p); return $this; }

    public function getTraiteePar(): ?Responsable { return $this->traiteePar; }
    public function setTraiteePar(?Responsable $r): static { $this->traiteePar = $r; return $this; }

    public function getPanne(): ?Panne { return $this->panne; }
    public function setPanne(?Panne $p): static { $this->panne = $p; return $this; }

    public function getSla(): ?SLA { return $this->sla; }
    public function setSla(?SLA $s): static { $this->sla = $s; return $this; }

    public function getEquipement(): ?Equipement { return $this->equipement; }
    public function setEquipement(?Equipement $e): static { $this->equipement = $e; return $this; }

    public function getHistoriquesTicket(): Collection { return $this->historiquesTicket; }
    public function getInterventions(): Collection { return $this->interventions; }

    public function addHistoriquesTicket(HistoriqueTicket $h): static
    {
        if (!$this->historiquesTicket->contains($h)) {
            $this->historiquesTicket->add($h);
            $h->setTicket($this);
        }
        return $this;
    }
    public function removeHistoriquesTicket(HistoriqueTicket $h): static
    {
        if ($this->historiquesTicket->removeElement($h) && $h->getTicket() === $this) {
            $h->setTicket(null);
        }
        return $this;
    }

    public function addIntervention(Intervention $i): static
    {
        if (!$this->interventions->contains($i)) {
            $this->interventions->add($i);
            $i->setTicket($this);
        }
        return $this;
    }
    public function removeIntervention(Intervention $i): static
    {
        if ($this->interventions->removeElement($i) && $i->getTicket() === $this) {
            $i->setTicket(null);
        }
        return $this;
    }

    public function getStatutLabel(): string
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getPrioriteLabel(): string
    {
        return self::PRIORITES[$this->priorite] ?? $this->priorite;
    }

    public function isEnRetard(): bool
    {
        $sla = $this->getSla();
        if (!$sla || in_array($this->statut, ['resolu', 'ferme'], true)) {
            return false;
        }
        $heures = (new \DateTime())->getTimestamp() - $this->date_creation->getTimestamp();
        return ($heures / 3600) > $sla->getTempsMaxResolution();
    }

    public function getHeuresOuverture(): float
    {
        $fin = $this->date_resolution ?? new \DateTime();
        return round(($fin->getTimestamp() - $this->date_creation->getTimestamp()) / 3600, 1);
    }
}
