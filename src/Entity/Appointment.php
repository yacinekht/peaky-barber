<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AppointmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
#[ORM\Table(name: 'appointment')]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Service::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Service $service;

    #[ORM\ManyToOne(targetEntity: Barber::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Barber $barber;

    #[ORM\Column(type: 'datetime')]
    #[Assert\NotBlank]
    private ?\DateTimeInterface $appointmentAt;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    private string $clientName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Email]
    private string $clientEmail;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private ?string $clientPhone = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $status = 'pending'; // pending, confirmed, cancelled

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int { return $this->id; }

    public function getService(): Service { return $this->service; }
    public function setService(Service $service): static { $this->service = $service; return $this; }

    public function getBarber(): Barber { return $this->barber; }
    public function setBarber(Barber $barber): static { $this->barber = $barber; return $this; }

    public function getAppointmentAt(): ?\DateTimeInterface { return $this->appointmentAt; }
    public function setAppointmentAt(?\DateTimeInterface $appointmentAt): static
    {
        $this->appointmentAt = $appointmentAt;
        return $this;
    }
    public function getClientName(): string { return $this->clientName; }
    public function setClientName(string $clientName): static { $this->clientName = $clientName; return $this; }

    public function getClientEmail(): string { return $this->clientEmail; }
    public function setClientEmail(string $clientEmail): static { $this->clientEmail = $clientEmail; return $this; }

    public function getClientPhone(): ?string { return $this->clientPhone; }
    public function setClientPhone(?string $clientPhone): static { $this->clientPhone = $clientPhone; return $this; }

    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): static { $this->status = $status; return $this; }

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function setCreatedAt(\DateTimeInterface $createdAt): static { $this->createdAt = $createdAt; return $this; }
}
