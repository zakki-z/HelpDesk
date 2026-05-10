<?php

namespace App\Entity;

use App\Repository\ResponsableRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ResponsableRepository::class)]
class Responsable extends Personnel
{
    #[ORM\Column(length: 255)]
    private ?string $service = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date_nomination = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'traiteePar')]
    private Collection $ticketsTraites;

    public function __construct()
    {
        parent::__construct();
        $this->ticketsTraites = new ArrayCollection();
    }

    public function getService(): ?string
    {
        return $this->service;
    }

    public function setService(string $service): static
    {
        $this->service = $service;
        return $this;
    }

    public function getDateNomination(): ?\DateTimeInterface
    {
        return $this->date_nomination;
    }

    public function setDateNomination(\DateTimeInterface $date_nomination): static
    {
        $this->date_nomination = $date_nomination;
        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicketsTraites(): Collection
    {
        return $this->ticketsTraites;
    }

    public function addTicketTraite(Ticket $ticket): static
    {
        if (!$this->ticketsTraites->contains($ticket)) {
            $this->ticketsTraites->add($ticket);
            $ticket->setTraiteePar($this);
        }
        return $this;
    }

    public function removeTicketTraite(Ticket $ticket): static
    {
        if ($this->ticketsTraites->removeElement($ticket)) {
            if ($ticket->getTraiteePar() === $this) {
                $ticket->setTraiteePar(null);
            }
        }
        return $this;
    }
}
