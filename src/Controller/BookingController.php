<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Appointment;
use App\Form\AppointmentType;
use App\Repository\AppointmentRepository;
use App\Repository\BarberRepository;
use App\Repository\ServiceRepository;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Attribute\Route;

class BookingController extends AbstractController
{
    private const DEFAULT_FROM = 'peakybarberkhaled@gmail.com';

    public function __construct(
        private AppointmentRepository $appointmentRepository,
        private BarberRepository $barberRepository,
        private ServiceRepository $serviceRepository,
        private LoggerInterface $logger,
        #[Autowire(env: 'default::MAIL_FROM')] private ?string $mailFrom = null,
        #[Autowire(env: 'default::SALON_EMAIL')] private ?string $salonEmail = null,
    ) {}

    /**
     * Adresses qui reçoivent le récapitulatif.
     * SALON_EMAIL peut contenir plusieurs adresses séparées par des virgules.
     * Sans SALON_EMAIL, le récap part sur l'adresse d'expédition.
     */
    private function salonRecipients(): array
    {
        $raw = $this->salonEmail ?: ($this->mailFrom ?: self::DEFAULT_FROM);
        $list = array_filter(array_map('trim', explode(',', $raw)));

        return array_values(array_filter(
            $list,
            fn (string $address) => filter_var($address, FILTER_VALIDATE_EMAIL) !== false
        ));
    }

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

            $from = new Address($this->mailFrom ?: self::DEFAULT_FROM, 'Peaky Barber');
            $recipients = $this->salonRecipients();
            $emailSent = false;

            // 1. Email de confirmation au client
            try {
                $email = (new Email())
                    ->from($from)
                    ->to($appointment->getClientEmail())
                    ->subject('Confirmation de votre réservation — Peaky Barber')
                    ->html($this->renderView('email/confirmation.html.twig', [
                        'appointment' => $appointment,
                    ]));
                if ($recipients) {
                    $email->replyTo($recipients[0]);
                }
                $mailer->send($email);
                $emailSent = true;
            } catch (\Throwable $e) {
                $this->logger->error('Email de confirmation non envoyé : ' . $e->getMessage());
            }

            // 2. Récapitulatif pour le salon / les employés
            if ($recipients) {
                try {
                    $notif = (new Email())
                        ->from($from)
                        ->to(...$recipients)
                        ->replyTo($appointment->getClientEmail())
                        ->subject(sprintf(
                            'Nouvelle réservation — %s, %s à %s',
                            $appointment->getClientName(),
                            $appointment->getAppointmentAt()->format('d/m/Y'),
                            $appointment->getAppointmentAt()->format('H:i')
                        ))
                        ->text(sprintf(
                            "Nouvelle réservation n°%d\n\nDate : %s à %s\nCoiffeur : %s\nPrestation : %s\nClient : %s\nEmail : %s\nTéléphone : %s\n",
                            $appointment->getId(),
                            $appointment->getAppointmentAt()->format('d/m/Y'),
                            $appointment->getAppointmentAt()->format('H:i'),
                            $appointment->getBarber()->getName(),
                            $appointment->getService()->getName(),
                            $appointment->getClientName(),
                            $appointment->getClientEmail(),
                            $appointment->getClientPhone() ?: 'non renseigné'
                        ))
                        ->html($this->renderView('email/salon_notification.html.twig', [
                            'appointment' => $appointment,
                        ]));
                    $mailer->send($notif);
                } catch (\Throwable $e) {
                    $this->logger->error('Récapitulatif salon non envoyé : ' . $e->getMessage());
                }
            }

            $this->addFlash('success', $emailSent
                ? 'Votre réservation a bien été enregistrée ! Un email de confirmation vous a été envoyé.'
                : 'Votre réservation a bien été enregistrée !');

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