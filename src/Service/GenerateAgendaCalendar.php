<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Agenda;
use App\Entity\AgendaSlot;
use DateInterval;
use DatePeriod;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

class GenerateAgendaCalendar
{
    /**
     * @var list<array{0: string, 1: string}>
     */
    private const array DAILY_AGENDA_SLOTS = [
        ['08:30', '09:00'],
        ['09:00', '09:30'],
        ['09:30', '10:00'],
        ['10:00', '10:30'],
        ['10:30', '11:00'],
        ['11:00', '11:30'],
        ['11:30', '12:00'],
        ['12:00', '12:30'],
        ['12:30', '13:00'],
        ['14:00', '14:30'],
        ['14:30', '15:00'],
        ['15:00', '15:30'],
        ['15:30', '16:00'],
        ['16:00', '16:30'],
        ['16:30', '17:00'],
        ['17:00', '17:30'],
        ['17:30', '18:00'],
        ['18:00', '18:30'],
        ['18:30', '19:00'],
        ['19:00', '19:30'],
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
    ) {
    }

    private static function getDatesBetween(
        string $firstDay,
        string $lastDay,
        bool $monday = true,
        bool $tuesday = true,
        bool $wednesday = true,
        bool $thursday = true,
        bool $friday = true,
        bool $saturday = true,
        bool $sunday = true,
    ): array {
        $interval = new DateInterval('P1D');

        $period = new DatePeriod(
            new DateTimeImmutable($firstDay),
            $interval,
            (new DateTimeImmutable($lastDay))->add($interval), // Add +1 day to include the last day
        );

        $dates = [];
        foreach ($period as $date) {
            $keep = match ($date->format('N')) {
                '1' => $monday,
                '2' => $tuesday,
                '3' => $wednesday,
                '4' => $thursday,
                '5' => $friday,
                '6' => $saturday,
                '7' => $sunday,
                default => false,
            };

            if (!$keep) {
                continue;
            }

            $dates[] =  $date;
        }

        return $dates;
    }

    public function generateCalendarFor(
        Agenda $agenda,
        string $firstDay,
        string $lastDay,
        bool $monday = true,
        bool $tuesday = true,
        bool $wednesday = true,
        bool $thursday = true,
        bool $friday = true,
        bool $saturday = true,
        bool $sunday = true,
    ): void {
        $dates = self::getDatesBetween($firstDay, $lastDay, $monday, $tuesday, $wednesday, $thursday, $friday, $saturday, $sunday);

        foreach ($dates as $date) {
            foreach (self::DAILY_AGENDA_SLOTS as $slot) {
                $this->entityManager->persist(
                    AgendaSlot::createOpen(
                        agenda: $agenda,
                        openingAt: $date->format('Y-m-d ') . $slot[0],
                        closingAt: $date->format('Y-m-d ') . $slot[1],
                    ),
                );
            }
        }
    }
}
