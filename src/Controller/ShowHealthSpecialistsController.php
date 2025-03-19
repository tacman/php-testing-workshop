<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\HealthSpecialist;
use App\Repository\DoctrineAgendaRepository;
use App\Repository\DoctrineAgendaSlotRepository;
use DateTimeImmutable;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class ShowHealthSpecialistsController extends AbstractController
{
    public function __construct(
        private readonly DoctrineAgendaRepository $agendaRepository,
        private readonly DoctrineAgendaSlotRepository $agendaSlotRepository,
    ) {
    }

    #[Route(
        path: '/health-specialists/{healthSpecialist}',
        name: 'app_health_specialist_show',
        defaults: ['section' => 'health_specialist'],
        methods: ['GET']
    )]
    public function __invoke(Request $request, HealthSpecialist $healthSpecialist): Response
    {
        try {
            $agenda = $this->agendaRepository->ownedBy($healthSpecialist);
        } catch (DomainException $e) {
            throw $this->createNotFoundException($e->getMessage(), previous: $e);
        }

        $date = DateTimeImmutable::createFromFormat(
            'Y-m-d',
            $request->query->getString('date', \date('Y-m-d')),
        );

        if (!$date instanceof DateTimeImmutable) {
            throw new BadRequestHttpException('Invalid date format');
        }

        return $this->render('health_specialist/show.html.twig', [
            'specialist' => $healthSpecialist,
            'agenda' => $agenda,
            'date' => $date,
            'tomorrow' => $date->modify('+1 day'),
            'availabilities' => $this->agendaSlotRepository->findByAgendaAndDate($agenda, $date),
            'opened_availabilities_count' => $this->agendaSlotRepository->countOpenByAgendaAndDate($agenda, $date),
        ]);
    }
}
