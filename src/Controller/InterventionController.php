<?php

namespace App\Controller;

use App\Entity\Intervention;
use App\Entity\LigneIntervention;
use App\Form\InterventionType;
use App\Repository\InterventionRepository;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/intervention')]
#[IsGranted('ROLE_TECHNICIEN')]
class InterventionController extends AbstractController
{
    #[Route('', name: 'app_intervention_index', methods: ['GET'])]
    public function index(InterventionRepository $repo): Response
    {
        $qb = $repo->createQueryBuilder('i')
            ->leftJoin('i.ticket', 't')
            ->leftJoin('i.technicien', 'tech')
            ->addSelect('t', 'tech')
            ->orderBy('i.date_debut', 'DESC');

        // Technicians only see their own interventions
        if (!$this->isGranted('ROLE_RESPONSABLE')) {
            $qb->andWhere('i.technicien = :user')->setParameter('user', $this->getUser());
        }

        return $this->render('intervention/index.html.twig', [
            'interventions' => $qb->getQuery()->getResult(),
        ]);
    }

    #[Route('/{id}', name: 'app_intervention_show', methods: ['GET'])]
    public function show(Intervention $intervention, StockRepository $stockRepo): Response
    {
        return $this->render('intervention/show.html.twig', [
            'intervention' => $intervention,
            'stocks'       => $stockRepo->findAll(),
        ]);
    }

    #[Route('/{id}/cloturer', name: 'app_intervention_cloturer', methods: ['POST'])]
    public function cloturer(Intervention $intervention, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('cloturer' . $intervention->getId(), $request->request->get('_token'))) {
            $intervention->setDateFin(new \DateTime());
            $commentaire = $request->request->get('commentaire');
            if ($commentaire) {
                $intervention->setCommentaire($commentaire);
            }
            $em->flush();
            $this->addFlash('success', 'Intervention clôturée.');
        }

        return $this->redirectToRoute('app_intervention_show', ['id' => $intervention->getId()]);
    }

    #[Route('/{id}/piece', name: 'app_intervention_piece', methods: ['POST'])]
    public function addPiece(Intervention $intervention, Request $request, EntityManagerInterface $em, StockRepository $stockRepo): Response
    {
        $stockId  = $request->request->get('stock_id');
        $quantite = (int) $request->request->get('quantite', 1);
        $stock    = $stockRepo->find($stockId);

        if ($stock && $quantite > 0 && $stock->getQuantite() >= $quantite) {
            $ligne = new LigneIntervention();
            $ligne->setIntervention($intervention);
            $ligne->setStock($stock);
            $ligne->setQuantiteUtilisee($quantite);

            $stock->setQuantite($stock->getQuantite() - $quantite);

            $em->persist($ligne);
            $em->flush();
            $this->addFlash('success', 'Pièce ajoutée à l\'intervention.');
        } else {
            $this->addFlash('error', 'Stock insuffisant ou article invalide.');
        }

        return $this->redirectToRoute('app_intervention_show', ['id' => $intervention->getId()]);
    }
}
