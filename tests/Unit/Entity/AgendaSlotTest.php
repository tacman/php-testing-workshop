<?php

declare(strict_types=1);

namespace App\Tests\Unit\Entity;

use App\Entity\Agenda;
use App\Entity\AgendaSlot;
use App\Entity\AgendaSlotStatus;
use App\Entity\HealthSpecialist;
use App\Entity\MedicalSpecialty;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class AgendaSlotTest extends TestCase
{
    public function testCreateOpenAgendaSlot(): void
    {
        // Arrange
        $agenda = new Agenda(
            new HealthSpecialist('John', 'Doe', MedicalSpecialty::CARDIOLOGIST),
        );

        $sut = AgendaSlot::createOpen($agenda, '2025-03-20 09:00', '2025-03-20 09:30');

        // Act & Assert
        self::assertSame($agenda, $sut->getAgenda());
        self::assertSame(AgendaSlotStatus::OPEN, $sut->getStatus());

        self::assertSame('2025-03-20T09:00:00+00:00', $sut->getOpeningAt()->format('c'));
        self::assertSame('2025-03-20T09:30:00+00:00', $sut->getClosingAt()->format('c'));

        self::assertEquals(new DateTimeImmutable('2025-03-20 09:00'), $sut->getOpeningAt());
        self::assertEquals(new DateTimeImmutable('2025-03-20 09:30'), $sut->getClosingAt());
    }

    public function testCreateAgendaSlotWithSwitchedOpeningAndClosingTimes(): void
    {
        // Arrange
        $agenda = new Agenda(
            new HealthSpecialist('John', 'Doe', MedicalSpecialty::CARDIOLOGIST),
        );

        // Assert
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Opening time must be before closing time');

        // Act
        AgendaSlot::createOpen($agenda, '2025-03-20 09:30', '2025-03-20 09:00');
    }
}