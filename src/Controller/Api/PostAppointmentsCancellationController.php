<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Api\MedicalAppointment\AppointmentCancellation;
use App\Entity\MedicalAppointment;
use App\Service\CancelAppointment;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class PostAppointmentsCancellationController extends AbstractController
{
    public function __construct(
        private readonly CancelAppointment $cancelAppointment,
        private readonly ValidatorInterface $validator,
    ) {
    }

    #[Route(
        path: '/appointments/{id}/cancellation',
        name: 'api_post_appointment_cancellation',
        methods: ['POST'],
    )]
    public function __invoke(
        #[MapEntity(mapping: ['id' => 'id'])] MedicalAppointment $appointment,
        Request $request,
    ): Response {
        $payload = $request->getPayload();

        $cancellation = new AppointmentCancellation($appointment);
        $cancellation->setReferenceNumber($payload->getString('referenceNumber'));
        $cancellation->setLastName($payload->getString('lastName'));
        $cancellation->setReason($payload->getString('reason'));

        $violations = $this->validator->validate($cancellation, groups: ['Default', 'Business']);

        if (\count($violations) > 0) {
            return $this->json($violations, status: Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $this->cancelAppointment->cancel($appointment, $cancellation->getReason());

        return $this->json($appointment, status: 201, context: [
            ObjectNormalizer::GROUPS => ['medical_appointment:read'],
        ]);
    }
}