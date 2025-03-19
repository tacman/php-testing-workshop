<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Agenda;
use App\Entity\AgendaSlot;
use App\Entity\AgendaSlotStatus;
use DateTimeInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use function Doctrine\ORM\QueryBuilder;

/**
 * @extends ServiceEntityRepository<AgendaSlot>
 */
class DoctrineAgendaSlotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AgendaSlot::class);
    }

    /**
     * @return AgendaSlot[]
     */
    public function findByMonth(Agenda $agenda, string $year, string $month): array
    {
        $qb = $this->createQueryBuilder('agenda_slot');

        $qb
            ->andWhere($qb->expr()->eq('agenda_slot.agenda', ':agenda'))
            ->andWhere('YEAR(agenda_slot.openingAt) = :year')
            ->andWhere('MONTH(agenda_slot.openingAt) = :month')
            ->orderBy('agenda_slot.openingAt', 'ASC')
            ->setParameter('agenda', $agenda)
            ->setParameter('year', $year)
            ->setParameter('month', $month);

        /** @var AgendaSlot[] */
        return $qb->getQuery()->getResult();
    }

    /**
     * @return AgendaSlot[]
     */
    public function findByAgendaAndDate(Agenda $agenda, DateTimeInterface $date): array
    {
        $qb = $this->createQueryBuilder('agenda_slot');

        $qb
            ->andWhere($qb->expr()->eq('agenda_slot.agenda', ':agenda'))
            ->andWhere('DATE(agenda_slot.openingAt) = :date')
            ->orderBy('agenda_slot.openingAt', 'ASC')
            ->setParameter('agenda', $agenda)
            ->setParameter('date', $date->format('Y-m-d'));

        /** @var AgendaSlot[] */
        return $qb->getQuery()->getResult();
    }

    public function countOpenByAgendaAndDate(Agenda $agenda, DateTimeInterface $date): int
    {
        $qb = $this->createQueryBuilder('agenda_slot');

        $qb
            ->select('COUNT(agenda_slot.id) AS count')
            ->andWhere($qb->expr()->eq('agenda_slot.agenda', ':agenda'))
            ->andWhere('DATE(agenda_slot.openingAt) = :date')
            ->andWhere('agenda_slot.status = :status')
            ->groupBy('agenda_slot.openingAt')
            ->orderBy('agenda_slot.openingAt', 'ASC')
            ->setParameter('agenda', $agenda)
            ->setParameter('date', $date->format('Y-m-d'))
            ->setParameter('status', AgendaSlotStatus::OPEN);

        /** @var array<int, array{count: int}> $results */
        $results = $qb->getQuery()->getArrayResult();

        return (int) \array_sum(\array_column($results, 'count'));
    }
}
