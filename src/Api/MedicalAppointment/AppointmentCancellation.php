<?php

declare(strict_types=1);

namespace App\Api\MedicalAppointment;

use App\Entity\MedicalAppointment;
use DateTimeImmutable;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\GroupSequence;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use function Symfony\Component\String\u;

#[GroupSequence(['AppointmentCancellation', 'Business'])]
final class AppointmentCancellation
{
    #[NotBlank]
    #[Length(min: 6, max: 6)]
    #[Regex('/^[A-Z0-9]{6}$/')]
    private string $referenceNumber = '';

    #[NotBlank]
    #[Length(max: 255)]
    private string $lastName = '';

    #[Length(max: 1024)]
    private string $reason = '';

    #[Callback(groups: ['Business'])]
    public static function validateSameReferenceNumber(
        self $appointmentCancellation,
        ExecutionContextInterface $validationContext,
    ): void {
        $appointment = $appointmentCancellation->getAppointment();

        $validReference = u($appointment->getReferenceNumber())->upper();
        $givenReference = u($appointmentCancellation->getReferenceNumber())->upper();

        if (!$givenReference->equalsTo($validReference)) {
            $validationContext
                ->buildViolation('Invalid appointment reference number.')
                ->atPath('referenceNumber')
                ->setInvalidValue($appointmentCancellation->getReferenceNumber())
                ->addViolation();
        }
    }

    #[Callback(groups: ['Business'])]
    public static function validateSameLastName(
        self $appointmentCancellation,
        ExecutionContextInterface $validationContext,
    ): void {
        $appointment = $appointmentCancellation->getAppointment();

        $validLastName = u($appointment->getFoldedLastName());
        $givenLastName = u($appointmentCancellation->getLastName())->folded()->lower();

        if (!$givenLastName->equalsTo($validLastName)) {
            $validationContext
                ->buildViolation('Invalid appointment patient last name.')
                ->atPath('lastName')
                ->setInvalidValue($appointmentCancellation->getLastName())
                ->addViolation();
        }
    }

    #[Callback]
    public static function validateIsUpcomingAppointment(
        self $appointmentCancellation,
        ExecutionContextInterface $validationContext,
    ): void {
        $appointment = $appointmentCancellation->getAppointment();

        $maxCancellationDateTime = DateTimeImmutable::createFromInterface($appointment->getOpeningAt())->modify('-30 minutes');

        if (new DateTimeImmutable('now') > $maxCancellationDateTime) {
            $validationContext
                ->buildViolation('Appointment is no longer cancellable.')
                ->addViolation();
        }
    }

    public function __construct(
        private readonly MedicalAppointment $appointment,
        private readonly DateTimeImmutable $at = new DateTimeImmutable(),
    ) {
    }

    public function getAppointment(): MedicalAppointment
    {
        return $this->appointment;
    }

    public function getAt(): DateTimeImmutable
    {
        return $this->at;
    }

    public function getReferenceNumber(): string
    {
        return $this->referenceNumber;
    }

    public function setReferenceNumber(string $referenceNumber): void
    {
        $this->referenceNumber = \trim($referenceNumber);
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = \trim($lastName);
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function setReason(string $reason): void
    {
        $this->reason = \trim($reason);
    }
}
