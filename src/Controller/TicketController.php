<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Entity\HistoriqueTicket;
use App\Entity\Intervention;
use App\Form\TicketType;
use App\Form\InterventionType;
use App\Repository\TicketRepository;
use App\Repository\TechnicienRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/ticket')]
#[IsGranted('ROLE_USER')]
class TicketController extends AbstractController
{
    #[Route('', name: 'app_ticket_index', methods: ['GET'])]
    public function index(TicketRepository $repo, Request $request): Response
    {
        $statut    = $request->query->get('statut');
        $priorite  = $request->query->get('priorite');
        $search    = $request->query->get('q');

        $qb = $repo->createQueryBuilder('t')
            ->leftJoin('t.creePar', 'c')
            ->leftJoin('t.traiteePar', 'tr')
            ->leftJoin('t.equipement', 'e')
            ->addSelect('c', 'tr', 'e')
            ->orderBy('t.date_creation', 'DESC');

        // Employees only see their own tickets
        if (!$this->isGranted('ROLE_TECHNICIEN')) {
            $qb->andWhere('t.creePar = :user')->setParameter('user', $this->getUser());
        }

        if ($statut) {
            $qb->andWhere('t.statut = :statut')->setParameter('statut', $statut);
        }
        if ($priorite) {
            $qb->andWhere('t.priorite = :priorite')->setParameter('priorite', $priorite);
        }
        if ($search) {
            $qb->andWhere('t.description LIKE :q')->setParameter('q', '%' . $search . '%');
        }

        return $this->render('ticket/index.html.twig', [
            'tickets'          => $qb->getQuery()->getResult(),
            'current_statut'   => $statut,
            'current_priorite' => $priorite,
            'search'           => $search,
            'statuts'          => Ticket::STATUTS,
            'priorites'        => Ticket::PRIORITES,
        ]);
    }

    #[Route('/nouveau', name: 'app_ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $ticket = new Ticket();
        $form   = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setDateCreation(new \DateTime());
            $ticket->setStatut('ouvert');
            $ticket->setCreePar($this->getUser());

            $em->persist($ticket);
            $em->flush();

            $this->addFlash('success', 'Ticket créé avec succès.');
            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        return $this->render('ticket/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_ticket_show', methods: ['GET', 'POST'])]
    public function show(
        Ticket $ticket,
        Request $request,
        EntityManagerInterface $em,
        TechnicienRepository $technicienRepo
    ): Response {
        // Access control: employees can only see their own tickets
        if (!$this->isGranted('ROLE_TECHNICIEN') && $ticket->getCreePar() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $interventionForm = null;
        if ($this->isGranted('ROLE_TECHNICIEN')) {
            $intervention = new Intervention();
            $intervention->setTicket($ticket);
            $interventionForm = $this->createForm(InterventionType::class, $intervention);
            $interventionForm->handleRequest($request);

            if ($interventionForm->isSubmitted() && $interventionForm->isValid()) {
                $em->persist($intervention);
                $em->flush();
                $this->addFlash('success', 'Intervention enregistrée.');
                return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
            }
        }

        return $this->render('ticket/show.html.twig', [
            'ticket'           => $ticket,
            'techniciens'      => $technicienRepo->findBy(['disponible' => true]),
            'statuts'          => Ticket::STATUTS,
            'intervention_form'=> $interventionForm?->createView(),
        ]);
    }

    #[Route('/{id}/statut', name: 'app_ticket_statut', methods: ['POST'])]
    #[IsGranted('ROLE_TECHNICIEN')]
    public function changeStatut(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $newStatut = $request->request->get('statut');

        if (!in_array($newStatut, array_keys(Ticket::STATUTS), true)) {
            $this->addFlash('error', 'Statut invalide.');
            return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
        }

        $ancien = $ticket->getStatut();
        $ticket->setStatut($newStatut);

        if ($newStatut === 'resolu') {
            $ticket->setDateResolution(new \DateTime());
        }
        if ($newStatut === 'ferme') {
            $ticket->setDateFermeture(new \DateTime());
        }

        // Record history
        $historique = new HistoriqueTicket();
        $historique->setTicket($ticket);
        $historique->setChampModifie('statut');
        $historique->setAncienneValeur($ancien);
        $historique->setNouvelleValeur($newStatut);
        $historique->setDateModification(new \DateTime());

        $em->persist($historique);
        $em->flush();

        $this->addFlash('success', 'Statut mis à jour.');
        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }

    #[Route('/{id}/assigner', name: 'app_ticket_assigner', methods: ['POST'])]
    #[IsGranted('ROLE_RESPONSABLE')]
    public function assigner(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        $technicienId = $request->request->get('technicien_id');

        // Update ticket status to "assigned" when a responsible assigns it
        $ancien = $ticket->getStatut();
        if ($ticket->getStatut() === 'ouvert') {
            $ticket->setStatut('assigne');
        }

        $historique = new HistoriqueTicket();
        $historique->setTicket($ticket);
        $historique->setChampModifie('statut');
        $historique->setAncienneValeur($ancien);
        $historique->setNouvelleValeur($ticket->getStatut());
        $historique->setDateModification(new \DateTime());

        $em->persist($historique);
        $em->flush();

        $this->addFlash('success', 'Ticket assigné.');
        return $this->redirectToRoute('app_ticket_show', ['id' => $ticket->getId()]);
    }

    #[Route('/{id}/supprimer', name: 'app_ticket_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RESPONSABLE')]
    public function delete(Ticket $ticket, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            $em->remove($ticket);
            $em->flush();
            $this->addFlash('success', 'Ticket supprimé.');
        }

        return $this->redirectToRoute('app_ticket_index');
    }
}
