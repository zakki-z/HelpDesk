<?php

namespace App\Controller;

use App\Entity\Stock;
use App\Entity\MouvementStock;
use App\Form\StockType;
use App\Repository\StockRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/stock')]
#[IsGranted('ROLE_RESPONSABLE')]
class StockController extends AbstractController
{
    #[Route('', name: 'app_stock_index', methods: ['GET'])]
    public function index(StockRepository $repo): Response
    {
        return $this->render('stock/index.html.twig', [
            'stocks'           => $repo->findAll(),
            'articles_critiques' => $repo->findCritiques(),
        ]);
    }

    #[Route('/nouveau', name: 'app_stock_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $stock = new Stock();
        $form  = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($stock);
            $em->flush();
            $this->addFlash('success', 'Article créé.');
            return $this->redirectToRoute('app_stock_index');
        }

        return $this->render('stock/new.html.twig', ['form' => $form]);
    }

    #[Route('/{id}', name: 'app_stock_show', methods: ['GET'])]
    public function show(Stock $stock): Response
    {
        return $this->render('stock/show.html.twig', ['stock' => $stock]);
    }

    #[Route('/{id}/modifier', name: 'app_stock_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Stock $stock, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(StockType::class, $stock);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Article modifié.');
            return $this->redirectToRoute('app_stock_index');
        }

        return $this->render('stock/edit.html.twig', ['form' => $form, 'stock' => $stock]);
    }

    #[Route('/{id}/mouvement', name: 'app_stock_mouvement', methods: ['POST'])]
    public function mouvement(Stock $stock, Request $request, EntityManagerInterface $em): Response
    {
        $type     = $request->request->get('type');
        $quantite = (int) $request->request->get('quantite', 0);

        if (!in_array($type, ['entree', 'sortie'], true) || $quantite <= 0) {
            $this->addFlash('error', 'Données invalides.');
            return $this->redirectToRoute('app_stock_show', ['id' => $stock->getId()]);
        }

        if ($type === 'sortie' && $stock->getQuantite() < $quantite) {
            $this->addFlash('error', 'Quantité insuffisante en stock.');
            return $this->redirectToRoute('app_stock_show', ['id' => $stock->getId()]);
        }

        $nouvelleQte = $type === 'entree'
            ? $stock->getQuantite() + $quantite
            : $stock->getQuantite() - $quantite;

        $stock->setQuantite($nouvelleQte);

        $mouvement = new MouvementStock();
        $mouvement->setStock($stock);
        $mouvement->setType($type);
        $mouvement->setQuantite($quantite);
        $mouvement->setDateMouvement(new \DateTime());
        $mouvement->setEffectuePar($this->getUser());

        $em->persist($mouvement);
        $em->flush();

        $this->addFlash('success', 'Mouvement enregistré.');
        return $this->redirectToRoute('app_stock_show', ['id' => $stock->getId()]);
    }

    #[Route('/{id}/supprimer', name: 'app_stock_delete', methods: ['POST'])]
    public function delete(Request $request, Stock $stock, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $stock->getId(), $request->request->get('_token'))) {
            $em->remove($stock);
            $em->flush();
            $this->addFlash('success', 'Article supprimé.');
        }

        return $this->redirectToRoute('app_stock_index');
    }
}
