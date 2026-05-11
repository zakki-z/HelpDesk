<?php

namespace App\Controller;

use App\Repository\PersonnelRepository;
use App\Repository\EquipementRepository;
use App\Repository\TicketRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        PersonnelRepository $personnelRepo,
        EquipementRepository $equipementRepo,
        TicketRepository $ticketRepo
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'total_personnel' => $personnelRepo->count([]),
            'total_equipements' => $equipementRepo->count([]),
            'total_tickets' => $ticketRepo->count([]),
        ]);
    }
}
