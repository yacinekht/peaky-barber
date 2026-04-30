<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Appointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Appointment::class);
    }

    public function save(Appointment $appointment): void
    {
        $em = $this->getEntityManager();
        $em->persist($appointment);
        $em->flush();
    }

    public function findUpcoming(): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.appointmentAt >= :now')
            ->setParameter('now', new \DateTime())
            ->orderBy('a.appointmentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByDateRange(\DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.appointmentAt BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('a.appointmentAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function countByStatus(string $status): int
    {
        return (int) $this->createQueryBuilder('a')
            ->select('COUNT(a.id)')
            ->where('a.status = :status')
            ->setParameter('status', $status)
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function findTakenSlots(int $barberId, string $date): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.appointmentAt')
            ->where('a.barber = :barberId')
            ->andWhere('DATE(a.appointmentAt) = :date')
            ->andWhere('a.status != :cancelled')
            ->setParameter('barberId', $barberId)
            ->setParameter('date', $date)
            ->setParameter('cancelled', 'cancelled')
            ->getQuery()
            ->getResult();
    }
}
