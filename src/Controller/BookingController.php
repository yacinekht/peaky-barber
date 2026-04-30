<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\BarberRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class BookingController extends AbstractController
{
    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private BarberRepository $barberRepository,
        private ServiceRepository $serviceRepository,
    ) {}

    #[Route('/', name: 'home')]
    public function home(): Response
    {
        return $this->render('booking/home.html.twig', [
            'services' => $this->serviceRepository->findActive(),
            'barbers'  => $this->barberRepository->findActive(),
        ]);
    }

    #[Route('/reservation', name: 'booking_new', methods: ['GET', 'POST'])]
    public function new(Request $request, MailerInterface $mailer): Response
    {
        $appointment = new Appointment();
        $form = $this->createForm(AppointmentType::class, $appointment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointment->setStatus('confirmed');
            $this->appointmentRepository->save($appointment);

            // Envoi email de confirmation
            try {
                $email = (new Email())
                    ->from('noreply@peakybarber.fr')
                    ->to($appointment->getClientEmail())
                    ->subject('Confirmation de votre réservation — Peaky Barber')
                    ->html($this->renderView('email/confirmation.html.twig', [
                        'appointment' => $appointment,
                    ]));
                $mailer->send($email);
            } catch (\Exception $e) {
                // Ne pas bloquer si l'email échoue
            }

            $this->addFlash('success', 'Votre réservation a bien été enregistrée ! Un email de confirmation vous a été envoyé.');
            return $this->redirectToRoute('booking_success', ['id' => $appointment->getId()]);
        }

        return $this->render('booking/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reservation/confirmation/{id}', name: 'booking_success')]
    public function success(Appointment $appointment): Response
    {
        return $this->render('booking/success.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/api/slots', name: 'api_slots', methods: ['GET'])]
    public function getSlots(Request $request): JsonResponse
    {
        $barberId = $request->query->getInt('barber');
        $date = $request->query->get('date');

        if (!$barberId || !$date) {
            return $this->json([]);
        }

        $takenSlots = $this->appointmentRepository->findTakenSlots($barberId, $date);
        $taken = array_map(fn($s) => $s['appointmentAt']->format('H:i'), $takenSlots);

        // Créneaux disponibles : 9h-19h toutes les 30min
        $slots = [];
        $start = new \DateTime("$date 09:00");
        $end   = new \DateTime("$date 19:00");
        while ($start < $end) {
            $time = $start->format('H:i');
            $slots[] = ['time' => $time, 'available' => !in_array($time, $taken)];
            $start->modify('+30 minutes');
        }

        return $this->json($slots);
    }
}
