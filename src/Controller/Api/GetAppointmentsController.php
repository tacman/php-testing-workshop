<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\MedicalAppointment;
use App\Repository\DoctrineMedicalAppointmentRepository;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Uid\Uuid;

use function Symfony\Component\String\u;

class GetAppointmentsController extends AbstractController
{
    public function __construct(
        private readonly DoctrineMedicalAppointmentRepository $appointmentRepository,
    ) {
    }

    #[Route(
        path: '/appointments',
        name: 'api_get_appointments',
        methods: ['GET'],
    )]
    public function __invoke(Request $request): Response
    {
        $filters = $request->query->all('filter');

        $criteria = [
            'date' => (new DateTimeImmutable('today'))->format('Y-m-d'),
            'practitioner' => null,
            'patient' => null,
            'reference' => null,
        ];

        if (\is_string($filters['date'] ?? null)) {
            $criteria['date'] = (new DateTimeImmutable($filters['date']))->format('Y-m-d');
        }

        if (\is_string($filters['practitioner'] ?? null) && Uuid::isValid($filters['practitioner'])) {
            $criteria['practitioner'] = $filters['practitioner'];
        }

        if (\is_string($filters['patient'] ?? null)) {
            $criteria['patient'] = $filters['patient'];
        }

        if (\is_string($filters['reference'] ?? null)) {
            $criteria['reference'] = $filters['reference'];
        }

        $queryBuilder = $this->appointmentRepository->createQueryBuilder('appointment');

        $queryBuilder
            ->addSelect('practitioner')
            ->join('appointment.practitioner', 'practitioner');

        $queryBuilder
            ->andWhere($queryBuilder->expr()->between('appointment.openingAt', ':openingAtAfter', ':openingAtBefore'))
            ->setParameter('openingAtAfter', $criteria['date'] . ' 00:00:00')
            ->setParameter('openingAtBefore', $criteria['date'] . ' 23:59:59');

        if ($criteria['reference'] !== null) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('appointment.referenceNumber', ':reference'))
                ->setParameter('reference', u($criteria['reference'])->upper()->toString());
        }

        if ($criteria['practitioner'] !== null) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('IDENTITY(appointment.practitioner)', ':practitioner'))
                ->setParameter('practitioner', $criteria['practitioner']);
        }

        if ($criteria['patient'] !== null) {
            $foldedPatientFirstName = u($filters['patient'])->folded()->lower()->toString();

            $queryBuilder
                ->andWhere(
                    $queryBuilder->expr()->orX(
                        $queryBuilder->expr()->like('IDENTITY(appointment.foldedPatientFirstName)', ':foldedPatientFirstName'),
                        $queryBuilder->expr()->like('IDENTITY(appointment.foldedPatientLastName)', ':foldedPatientLastName'),
                    ),
                )
                ->setParameter('foldedPatientFirstName', '%' . $foldedPatientFirstName . '%')
                ->setParameter('foldedPatientFirstName', '%' . $foldedPatientFirstName . '%');
        }

        $queryBuilder
            ->addOrderBy('practitioner.lastName', 'ASC')
            ->addOrderBy('practitioner.firstName', 'ASC')
            ->addOrderBy('appointment.openingAt', 'ASC');

        /** @var MedicalAppointment[] $appointments */
        $appointments = $queryBuilder->getQuery()->getResult();

        return $this->json($appointments, context: [
            ObjectNormalizer::GROUPS => ['medical_appointment:read'],
        ]);
    }
}