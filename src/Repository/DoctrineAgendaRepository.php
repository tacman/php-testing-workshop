<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Agenda;
use App\Entity\HealthSpecialist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use DomainException;

/**
 * @extends ServiceEntityRepository<Agenda>
 */
class DoctrineAgendaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Agenda::class);
    }

    public function ownedBy(HealthSpecialist $owner): Agenda
    {
        $agenda = $this->findOneBy(['owner' => $owner]);

        if (!$agenda instanceof Agenda) {
            throw new DomainException('Agenda not found');
        }

        return $agenda;
    }
}
