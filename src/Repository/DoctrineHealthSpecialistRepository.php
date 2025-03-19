<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\HealthSpecialist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<HealthSpecialist>
 */
class DoctrineHealthSpecialistRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HealthSpecialist::class);
    }
}
