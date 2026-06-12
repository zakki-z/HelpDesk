<?php

namespace App\Controller;

use App\Entity\SLA;
use App\Form\SLAType;
use App\Repository\SLARepository;
use App\Repository\TicketRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sla')]
#[IsGranted('ROLE_RESPONSABLE')]
class SlaController extends AbstractController
{
    #[Route('', name: 'app_sla_index', methods: ['GET'])]
    public function index(SLARepository $slaRepo, TicketRepository $ticketRepo): Response
    {
        $slas   = $slaRepo->findAll();
        $stats  = $this->buildStats($ticketRepo);

        return $this->render('sla/index.html.twig', [
            'slas'  => $slas,
            'stats' => $stats,
        ]);
    }

    #[Route('/nouveau', name: 'app_sla_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $sla  = new SLA();
        $form = $this->createForm(SLAType::class, $sla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($sla);
            $em->flush();
            $this->addFlash('success', 'SLA créé.');
            return $this->redirectToRoute('app_sla_index');
        }

        return $this->render('sla/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}/modifier', name: 'app_sla_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, SLA $sla, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SLAType::class, $sla);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'SLA modifié.');
            return $this->redirectToRoute('app_sla_index');
        }

        return $this->render('sla/edit.html.twig', ['form' => $form, 'sla' => $sla]);
    }

    #[Route('/{id}/supprimer', name: 'app_sla_delete', methods: ['POST'])]
    public function delete(Request $request, SLA $sla, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $sla->getId(), $request->request->get('_token'))) {
            $em->remove($sla);
            $em->flush();
            $this->addFlash('success', 'SLA supprimé.');
        }

        return $this->redirectToRoute('app_sla_index');
    }

    // -----------------------------------------------------------------------
    // Helpers
    // -----------------------------------------------------------------------

    private function buildStats(TicketRepository $ticketRepo): array
    {
        $tickets = $ticketRepo->findAll();
        $now     = new \DateTime();

        $total       = count($tickets);
        $enRetard    = 0;
        $resolus     = 0;
        $nonResolus  = 0;
        $totalTemps  = 0;
        $countTemps  = 0;

        foreach ($tickets as $ticket) {
            $sla = $ticket->getSla();

            if (in_array($ticket->getStatut(), ['resolu', 'ferme'], true)) {
                $resolus++;
                if ($ticket->getDateResolution() && $ticket->getDateCreation()) {
                    $diff = $ticket->getDateCreation()->diff($ticket->getDateResolution());
                    $totalTemps += $diff->h + ($diff->days * 24);
                    $countTemps++;
                }

                // Check SLA breach: was it resolved within the max resolution time?
                if ($sla && $ticket->getDateResolution() && $ticket->getDateCreation()) {
                    $heuresReelles = ($ticket->getDateResolution()->getTimestamp() - $ticket->getDateCreation()->getTimestamp()) / 3600;
                    if ($heuresReelles > $sla->getTempsMaxResolution()) {
                        $enRetard++;
                    }
                }
            } else {
                $nonResolus++;
                // Open tickets: check if they've exceeded SLA resolution time
                if ($sla) {
                    $heuresOuvert = ($now->getTimestamp() - $ticket->getDateCreation()->getTimestamp()) / 3600;
                    if ($heuresOuvert > $sla->getTempsMaxResolution()) {
                        $enRetard++;
                    }
                }
            }
        }

        $tauxRespect = $total > 0 ? round((($total - $enRetard) / $total) * 100, 1) : 100;
        $tempsMoyen  = $countTemps > 0 ? round($totalTemps / $countTemps, 1) : null;

        return [
            'total'        => $total,
            'en_retard'    => $enRetard,
            'resolus'      => $resolus,
            'non_resolus'  => $nonResolus,
            'taux_respect' => $tauxRespect,
            'temps_moyen'  => $tempsMoyen,
        ];
    }
}
