<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Repository\PersonnelRepository;
use App\Repository\EquipementRepository;
use App\Repository\TicketRepository;
use App\Repository\StockRepository;
use App\Repository\InterventionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        PersonnelRepository   $personnelRepo,
        EquipementRepository  $equipementRepo,
        TicketRepository      $ticketRepo,
        StockRepository       $stockRepo,
        InterventionRepository $interventionRepo,
    ): Response {
        $user = $this->getUser();
        $now  = new \DateTime();

        // ---- Shared stats ----
        $mesTickets = $ticketRepo->createQueryBuilder('t')
            ->andWhere('t.creePar = :user')
            ->setParameter('user', $user)
            ->orderBy('t.date_creation', 'DESC')
            ->setMaxResults(5)
            ->getQuery()->getResult();

        $mesTicketsParStatut = [];
        foreach (array_keys(Ticket::STATUTS) as $s) {
            $mesTicketsParStatut[$s] = $ticketRepo->count(['creePar' => $user, 'statut' => $s]);
        }

        // ---- Technician stats ----
        $interventionsEnCours = [];
        if ($this->isGranted('ROLE_TECHNICIEN')) {
            $interventionsEnCours = $interventionRepo->createQueryBuilder('i')
                ->andWhere('i.technicien = :user')
                ->andWhere('i.date_fin IS NULL')
                ->setParameter('user', $user)
                ->setMaxResults(5)
                ->getQuery()->getResult();
        }

        // ---- Admin / Responsable stats ----
        $ticketsParStatut   = [];
        $ticketsParPriorite = [];
        $articlesEnAlerte   = [];
        $ticketsRecents     = [];
        $ticketsEnRetard    = [];

        if ($this->isGranted('ROLE_RESPONSABLE')) {
            foreach (array_keys(Ticket::STATUTS) as $s) {
                $ticketsParStatut[$s] = $ticketRepo->count(['statut' => $s]);
            }
            foreach (array_keys(Ticket::PRIORITES) as $p) {
                $ticketsParPriorite[$p] = $ticketRepo->count(['priorite' => $p]);
            }

            $articlesEnAlerte = $stockRepo->findCritiques();

            $ticketsRecents = $ticketRepo->createQueryBuilder('t')
                ->orderBy('t.date_creation', 'DESC')
                ->setMaxResults(8)
                ->getQuery()->getResult();

            // Tickets potentially breaching SLA (open > sla resolution time)
            $ticketsEnRetard = array_filter(
                $ticketRepo->createQueryBuilder('t')
                    ->leftJoin('t.sla', 's')
                    ->addSelect('s')
                    ->andWhere("t.statut NOT IN ('resolu','ferme')")
                    ->getQuery()->getResult(),
                function (Ticket $t) use ($now) {
                    $sla = $t->getSla();
                    if (!$sla) return false;
                    $heures = ($now->getTimestamp() - $t->getDateCreation()->getTimestamp()) / 3600;
                    return $heures > $sla->getTempsMaxResolution();
                }
            );
        }

        return $this->render('dashboard/index.html.twig', [
            'total_personnel'       => $personnelRepo->count([]),
            'total_equipements'     => $equipementRepo->count([]),
            'total_tickets'         => $ticketRepo->count([]),
            'total_stocks'          => $stockRepo->count([]),
            'mes_tickets'           => $mesTickets,
            'mes_tickets_par_statut'=> $mesTicketsParStatut,
            'interventions_en_cours'=> $interventionsEnCours,
            'tickets_par_statut'    => $ticketsParStatut,
            'tickets_par_priorite'  => $ticketsParPriorite,
            'articles_en_alerte'    => $articlesEnAlerte,
            'tickets_recents'       => $ticketsRecents,
            'tickets_en_retard'     => $ticketsEnRetard,
        ]);
    }
}
