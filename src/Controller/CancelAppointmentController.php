<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\CancelAppointment;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CancelAppointmentController extends AbstractController
{
    public function __construct(
        private readonly CancelAppointment $cancelAppointment,
    ) {
    }

    #[Route(
        path: '/appointments/{id}/cancel',
        name: 'app_appointment_cancel',
        defaults: ['section' => 'health_specialist'],
        methods: ['POST'],
    )]
    public function __invoke(Request $request, string $id): Response
    {
        $tokenId = 'cancel-appointment-' . $id;
        $token = $request->getPayload()->getString('_token');

        if (!$this->isCsrfTokenValid($tokenId, $token)) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }

        try {
            $this->cancelAppointment->cancelByAppointmentId(
                appointmentId: $id,
                reason: $request->getPayload()->getString('cancellation_reason'),
            );

            $this->addFlash('success', 'Appointment has been cancelled successfully.');
        } catch (DomainException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('app_appointment_show', ['id' => $id]);
    }
}
