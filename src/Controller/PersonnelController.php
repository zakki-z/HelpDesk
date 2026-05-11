<?php

namespace App\Controller;

use App\Entity\Personnel;
use App\Entity\Technicien;
use App\Entity\Responsable;
use App\Form\PersonnelType;
use App\Form\TechnicienType;
use App\Form\ResponsableType;
use App\Repository\PersonnelRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/personnel')]
#[IsGranted('ROLE_RESPONSABLE')]
class PersonnelController extends AbstractController
{
    #[Route('', name: 'app_personnel_index', methods: ['GET'])]
    public function index(PersonnelRepository $repo): Response
    {
        return $this->render('personnel/index.html.twig', [
            'personnels' => $repo->findAll(),
        ]);
    }

    #[Route('/nouveau/{type}', name: 'app_personnel_new', methods: ['GET', 'POST'], defaults: ['type' => 'personnel'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher,
        string $type
    ): Response {
        $entity = match ($type) {
            'technicien' => new Technicien(),
            'responsable' => new Responsable(),
            default => new Personnel(),
        };

        $formClass = match ($type) {
            'technicien' => TechnicienType::class,
            'responsable' => ResponsableType::class,
            default => PersonnelType::class,
        };

        $form = $this->createForm($formClass, $entity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain password
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $entity->setPassword($hasher->hashPassword($entity, $plainPassword));
            }

            // Set roles based on type
            $roles = match ($type) {
                'technicien' => ['ROLE_TECHNICIEN'],
                'responsable' => ['ROLE_RESPONSABLE'],
                default => ['ROLE_USER'],
            };
            $entity->setRoles($roles);

            $em->persist($entity);
            $em->flush();

            $this->addFlash('success', 'Personnel créé avec succès.');
            return $this->redirectToRoute('app_personnel_index');
        }

        return $this->render('personnel/new.html.twig', [
            'form' => $form,
            'type' => $type,
        ]);
    }

    #[Route('/{id}', name: 'app_personnel_show', methods: ['GET'])]
    public function show(Personnel $personnel): Response
    {
        return $this->render('personnel/show.html.twig', [
            'personnel' => $personnel,
        ]);
    }

    #[Route('/{id}/modifier', name: 'app_personnel_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Personnel $personnel,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $hasher
    ): Response {
        $formClass = match (true) {
            $personnel instanceof Technicien => TechnicienType::class,
            $personnel instanceof Responsable => ResponsableType::class,
            default => PersonnelType::class,
        };

        $type = match (true) {
            $personnel instanceof Technicien => 'technicien',
            $personnel instanceof Responsable => 'responsable',
            default => 'personnel',
        };

        $form = $this->createForm($formClass, $personnel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                $personnel->setPassword($hasher->hashPassword($personnel, $plainPassword));
            }

            $em->flush();

            $this->addFlash('success', 'Personnel modifié avec succès.');
            return $this->redirectToRoute('app_personnel_index');
        }

        return $this->render('personnel/edit.html.twig', [
            'form' => $form,
            'personnel' => $personnel,
            'type' => $type,
        ]);
    }

    #[Route('/{id}/supprimer', name: 'app_personnel_delete', methods: ['POST'])]
    public function delete(Request $request, Personnel $personnel, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $personnel->getId(), $request->request->get('_token'))) {
            $em->remove($personnel);
            $em->flush();
            $this->addFlash('success', 'Personnel supprimé avec succès.');
        }

        return $this->redirectToRoute('app_personnel_index');
    }
}
