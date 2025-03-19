<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DoctrineAgendaSlotRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

#[ORM\UniqueConstraint(name: 'agenda_slot_window_unique', fields: ['agenda', 'openingAt', 'closingAt'])]
#[ORM\Entity(repositoryClass: DoctrineAgendaSlotRepository::class)]
class AgendaSlot
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    private readonly string $id;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private readonly Agenda $agenda;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $openingAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private readonly DateTimeImmutable $closingAt;

    #[ORM\Column(enumType: AgendaSlotStatus::class)]
    private AgendaSlotStatus $status;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    public static function createOpen(Agenda $agenda, string $openingAt, string $closingAt): self
    {
        return self::create($agenda, AgendaSlotStatus::OPEN, $openingAt, $closingAt);
    }

    public static function createBlocked(Agenda $agenda, string $openingAt, string $closingAt): self
    {
        return self::create($agenda, AgendaSlotStatus::BLOCKED, $openingAt, $closingAt);
    }

    public static function createBooked(Agenda $agenda, string $openingAt, string $closingAt): self
    {
        return self::create($agenda, AgendaSlotStatus::BOOKED, $openingAt, $closingAt);
    }

    public static function create(Agenda $agenda, AgendaSlotStatus $status, string $openingAt, string $closingAt): self
    {
        $openingAt = new DateTimeImmutable($openingAt);
        $closingAt = new DateTimeImmutable($closingAt);

        if ($openingAt >= $closingAt) {
            throw new InvalidArgumentException('Opening time must be before closing time');
        }

        return new self($agenda, $status, $openingAt, $closingAt);
    }

    public function __construct(
        Agenda $agenda,
        AgendaSlotStatus $status,
        DateTimeImmutable $openingAt,
        DateTimeImmutable $closingAt,
    ) {
        $this->id = (string) Uuid::v4();
        $this->agenda = $agenda;
        $this->status = $status;
        $this->openingAt = $openingAt;
        $this->closingAt = $closingAt;
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getAgenda(): Agenda
    {
        return $this->agenda;
    }

    public function getPractitioner(): HealthSpecialist
    {
        return $this->getAgenda()->getOwner();
    }

    public function getOpeningAt(): DateTimeInterface
    {
        return $this->openingAt;
    }

    public function getClosingAt(): DateTimeInterface
    {
        return $this->closingAt;
    }

    public function getStatus(): AgendaSlotStatus
    {
        return $this->status;
    }

    public function setStatus(AgendaSlotStatus $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function book(?DateTimeInterface $bookedAt = null): void
    {
        // TODO: check that the status is open for booking
        $this->status = AgendaSlotStatus::BOOKED;
        $this->updatedAt = $bookedAt instanceof DateTimeInterface
            ? DateTimeImmutable::createFromInterface($bookedAt)
            : new DateTimeImmutable();
    }
}
