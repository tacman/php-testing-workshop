<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\MedicalAppointment;
use App\Repository\DoctrineMedicalAppointmentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowAppointmentController extends AbstractController
{
    public function __construct(
        private readonly DoctrineMedicalAppointmentRepository $appointmentRepository,
    ) {
    }

    #[Route(
        path: '/appointments/{id}',
        name: 'app_appointment_show',
        defaults: ['section' => 'health_specialist'],
        methods: ['GET'],
    )]
    public function __invoke(string $id): Response
    {
        $appointment = $this->appointmentRepository->find($id);

        if (!$appointment instanceof MedicalAppointment) {
            throw $this->createNotFoundException(\sprintf('Medical appointment %s not found.', $id));
        }

        // TODO: prevent from accessing the appointment details when it's expired

        return $this->render('appointment/show.html.twig', [
            'appointment' => $appointment,
            'specialist' => $appointment->getPractitioner(),
        ]);
    }
}
