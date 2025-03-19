<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\AgendaSlot;
use App\Entity\AgendaSlotStatus;
use App\Entity\MedicalAppointment;
use App\Form\MedicalAppointmentBookingType;
use App\Model\MedicalAppointmentBooking;
use App\Repository\DoctrineAgendaSlotRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

final class BookAppointmentController extends AbstractController
{
    public function __construct(
        private readonly DoctrineAgendaSlotRepository $agendaSlotRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    #[Route(
        path: '/appointments',
        name: 'app_appointment_book',
        defaults: ['section' => 'health_specialist'],
        methods: ['GET', 'POST'],
    )]
    public function __invoke(
        Request $request,
        #[MapQueryParameter(validationFailedStatusCode: Response::HTTP_BAD_REQUEST)] string $availabilityId,
    ): Response {
        $availability = $this->agendaSlotRepository->find($availabilityId);

        if (!$availability instanceof AgendaSlot) {
            throw $this->createNotFoundException(\sprintf('Availability %s not found.', $availabilityId));
        }

        if ($availability->getStatus() !== AgendaSlotStatus::OPEN) {
            throw $this->createNotFoundException(\sprintf('Availability %s not bookable.', $availabilityId));
        }

        // User should not be able to book an appointment less than 30 minutes before the opening time
        $minBookingDateTime = $availability->getOpeningAt()->modify('-30 minutes');
        if (new DateTimeImmutable() >= $minBookingDateTime) {
            throw $this->createNotFoundException(\sprintf('Availability %s can no longer be booked.', $availabilityId));
        }

        $dto = new MedicalAppointmentBooking();

        $form = $this
            ->createForm(MedicalAppointmentBookingType::class, $dto)
            ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: refactor using a semantic constructor
            $appointment = new MedicalAppointment(
                $availability->getPractitioner(),
                $availability->getOpeningAt(),
                $availability->getClosingAt(),
                $this->generateReferenceNumber(),
                $dto->getFirstName(),
                $dto->getLastName(),
            );

            $appointment->setEmail($dto->getEmail());
            $appointment->setPhone($dto->getPhone());

            $availability->book($appointment->getCreatedAt());

            $this->entityManager->persist($appointment);
            $this->entityManager->persist($availability);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_appointment_show', [
                'id' => (string) $appointment->getId(),
            ]);
        }

        return $this->render('appointment/book.html.twig', [
            'availability' => $availability,
            'specialist' => $availability->getPractitioner(),
            'form' => $form->createView(),
        ]);
    }

    // TODO: refactor within a dedicated service class
    private function generateReferenceNumber(): string
    {
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $chars = \str_shuffle($chars);

        $limit = \strlen($chars) - 1;
        $referenceNumber = '';
        for ($i = 1; $i <= 6; $i++) {
            $referenceNumber .= $chars[\random_int(0, $limit)];
        }

        // TODO: check if the reference number already exists in the database

        return $referenceNumber;
    }
}
