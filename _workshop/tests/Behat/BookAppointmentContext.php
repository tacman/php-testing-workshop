<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\AgendaSlotStatus;
use App\Entity\HealthSpecialist;
use App\Entity\MedicalSpecialty;
use App\Factory\AgendaFactory;
use App\Factory\AgendaSlotFactory;
use App\Factory\HealthSpecialistFactory;
use App\Factory\MedicalAppointmentFactory;
use Assert\Assertion;
use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Behat\Transformation\Transform;
use DateTimeImmutable;
use Zenstruck\Foundry\Persistence\Proxy;

final class BookAppointmentContext extends AcceptanceTestCase implements Context
{
    /**
     * @var array<string, Proxy<HealthSpecialist>>
     */
    private array $specialists = [];

    // TODO: to be implemented...
}