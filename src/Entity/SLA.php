<?php

namespace App\Entity;

use App\Repository\SLARepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SLARepository::class)]
#[ORM\Table(name: 'sla')]
class SLA
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $temps_max_resolution = null;

    #[ORM\Column]
    private ?int $temps_max_reponse = null;

    /**
     * @var Collection<int, Ticket>
     */
    #[ORM\OneToMany(targetEntity: Ticket::class, mappedBy: 'sla')]
    private Collection $tickets;

    public function __construct()
    {
        $this->tickets = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTempsMaxResolution(): ?int
    {
        return $this->temps_max_resolution;
    }

    public function setTempsMaxResolution(int $temps_max_resolution): static
    {
        $this->temps_max_resolution = $temps_max_resolution;
        return $this;
    }

    public function getTempsMaxReponse(): ?int
    {
        return $this->temps_max_reponse;
    }

    public function setTempsMaxReponse(int $temps_max_reponse): static
    {
        $this->temps_max_reponse = $temps_max_reponse;
        return $this;
    }

    /**
     * @return Collection<int, Ticket>
     */
    public function getTickets(): Collection
    {
        return $this->tickets;
    }
}
