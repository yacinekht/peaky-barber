<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Entity\Appointment;
use App\Entity\Barber;
use App\Entity\Service;
use App\Repository\AppointmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
    ) {}

    public function index(): Response
    {
        $pending   = $this->appointmentRepository->countByStatus('pending');
        $confirmed = $this->appointmentRepository->countByStatus('confirmed');
        $cancelled = $this->appointmentRepository->countByStatus('cancelled');
        $upcoming  = $this->appointmentRepository->findUpcoming();

        return $this->render('admin/dashboard.html.twig', [
            'pending'   => $pending,
            'confirmed' => $confirmed,
            'cancelled' => $cancelled,
            'upcoming'  => array_slice($upcoming, 0, 10),
        ]);
    }

    #[Route('/test-update', name: 'test_update')]
    public function testUpdate(EntityManagerInterface $em): Response
    {
        try {
            $service = $em->getRepository(Service::class)->find(1);

            $service->setName('TEST ' . time());

            $em->flush();

            return new Response('OK');
        } catch (\Throwable $e) {
            return new Response(
                get_class($e) . "\n\n" .
                $e->getMessage() . "\n\n" .
                $e->getTraceAsString()
            );
        }
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<img src="/images/logo.png" alt="Peaky Barber" style="height:32px"> Peaky Barber')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-tachometer-alt');

        yield MenuItem::section('Réservations');
        yield MenuItem::linkToCrud('Réservations', 'fa fa-calendar-check', Appointment::class);

        yield MenuItem::section('Équipe & Services');
        yield MenuItem::linkToCrud('Coiffeurs', 'fa fa-scissors', Barber::class);
        yield MenuItem::linkToCrud('Services & Prix', 'fa fa-tags', Service::class);

        yield MenuItem::section('Site');
        yield MenuItem::linkToRoute('Voir le site', 'fa fa-eye', 'home');
    }
}