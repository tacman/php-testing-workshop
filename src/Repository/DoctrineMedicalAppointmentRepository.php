<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\MedicalAppointment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MedicalAppointment>
 */
class DoctrineMedicalAppointmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MedicalAppointment::class);
    }
}
