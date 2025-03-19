<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\MedicalAppointment;
use App\Repository\DoctrineMedicalAppointmentRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use DomainException;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Uid\Uuid;

class CancelAppointment
{
    public function __construct(
        private readonly DoctrineMedicalAppointmentRepository $appointmentRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly MailerInterface $mailer,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function cancel(MedicalAppointment $appointment, ?string $reason = null): void
    {
        // TODO: check if the appointment is cancellable

        $appointment->cancel(new DateTimeImmutable(), $reason);

        $this->entityManager->flush();

        $this->logger->info('Medical appointment {appointmentId} has been cancelled.', [
            'appointmentId' => (string) $appointment->getId(),
            'reason' => $reason,
        ]);

        $this->sendConfirmationEmail($appointment);

        // TODO: send asynchronous notification with Symfony Messenger
    }

    public function cancelByReferenceNumber(string $referenceNumber, ?string $reason = null): void
    {
        $appointment = $this->appointmentRepository->findOneBy(['referenceNumber' => mb_strtoupper($referenceNumber)]);

        if (!$appointment instanceof MedicalAppointment) {
            throw new DomainException(\sprintf('The appointment "%s" does not exist.', $referenceNumber));
        }

        $this->cancel($appointment, $reason);
    }

    public function cancelByAppointmentId(Uuid|string $appointmentId, ?string $reason = null): void
    {
        $appointment = $this->appointmentRepository->find($appointmentId);

        if (!$appointment instanceof MedicalAppointment) {
            throw new DomainException(\sprintf('The appointment "%s" does not exist.', $appointmentId));
        }

        $this->cancel($appointment, $reason);
    }

    private function sendConfirmationEmail(MedicalAppointment $appointment): void
    {
        $email = (new TemplatedEmail())
            ->from('no-reply@example.com')
            ->replyTo('contact@example.com')
            ->to(new Address((string) $appointment->getEmail(), $appointment->getFullName()))
            ->subject('Your appointment has been cancelled')
            ->textTemplate('email/appointment_cancelled.txt.twig')
            ->context(['appointment' => $appointment]);

        $this->mailer->send($email);
    }
}
