<?php

declare(strict_types=1);

namespace App\Tests\EndToEnd;

use App\Entity\MedicalSpecialty;
use App\Factory\AgendaFactory;
use App\Factory\HealthSpecialistFactory;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use DateTimeImmutable;
use Symfony\Component\Panther\PantherTestCase;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

final class BookAppointmentFeatureTest extends PantherTestCase
{
    use Factories;
    use HasBrowser;
    use ResetDatabase;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        StaticDriver::setKeepStaticConnections(false);
    }

    public static function tearDownAfterClass(): void
    {
        StaticDriver::setKeepStaticConnections(true);

        parent::tearDownAfterClass();
    }

    public function testBookADentistAppointment(): void
    {
        $specialist = HealthSpecialistFactory::createOne([
            'firstName' => 'Kevin',
            'lastName' => 'Costner',
            'specialty' => MedicalSpecialty::DENTIST,
        ]);

        AgendaFactory::new(['owner' => $specialist])
            ->published()
            ->withCalendar('today', 'tomorrow')
            ->create();

        // TODO: to be implemented...
    }
}
