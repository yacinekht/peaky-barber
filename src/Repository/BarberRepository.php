<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Barber;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BarberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Barber::class);
    }

    public function save(Barber $barber): void
    {
        $em = $this->getEntityManager();
        $em->persist($barber);
        $em->flush();
    }

    public function findActive(): array
    {
        return $this->findBy(['isActive' => true], ['name' => 'ASC']);
    }
}
