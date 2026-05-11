<?php

namespace App\Controller;

use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/equipement')]
#[IsGranted('ROLE_USER')]
class EquipementController extends AbstractController
{
    #[Route('', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $repo, Request $request): Response
    {
        $type = $request->query->get('type');
        $search = $request->query->get('q');

        $qb = $repo->createQueryBuilder('e');

        if ($type) {
            $qb->andWhere('e.type = :type')->setParameter('type', $type);
        }

        if ($search) {
            $qb->andWhere('e.nom LIKE :search')->setParameter('search', '%' . $search . '%');
        }

        $qb->orderBy('e.nom', 'ASC');

        return $this->render('equipement/index.html.twig', [
            'equipements' => $qb->getQuery()->getResult(),
            'types' => $repo->findDistinctTypes(),
            'current_type' => $type,
            'search' => $search,
        ]);
    }

    #[Route('/nouveau', name: 'app_equipement_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RESPONSABLE')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($equipement);
            $em->flush();

            $this->addFlash('success', 'Équipement créé avec succès.');
            return $this->redirectToRoute('app_equipement_index');
        }

        return $this->render('equipement/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipement_show', methods: ['GET'])]
    public function show(Equipement $equipement): Response
    {
        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_equipement_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RESPONSABLE')]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Équipement modifié avec succès.');
            return $this->redirectToRoute('app_equipement_index');
        }

        return $this->render('equipement/edit.html.twig', [
            'form' => $form,
            'equipement' => $equipement,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_equipement_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RESPONSABLE')]
    public function delete(Request $request, Equipement $equipement, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $equipement->getId(), $request->request->get('_token'))) {
            $em->remove($equipement);
            $em->flush();
            $this->addFlash('success', 'Équipement supprimé avec succès.');
        }

        return $this->redirectToRoute('app_equipement_index');
    }
}
