<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Factory\MedicalAppointmentFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Mailer\Test\InteractsWithMailer;

final class PostAppointmentsCancellationControllerTest extends WebTestCase
{
    use Factories;
    use InteractsWithMailer;
    use ResetDatabase;

    /*use HasBrowser {
        browser as baseKernelBrowser;
    }*/

    public function testCancelScheduledUpcomingAppointment(): void
    {
        $appointment = MedicalAppointmentFactory::new()
            ->scheduled('E4N6ST')
            ->tomorrowAt('10:00')
            ->forPatient('John', 'Smith')
            ->create();

        self::ensureKernelShutdown();

        $client = static::createClient();
        $client->enableProfiler();
        $client->setServerParameter('HTTP_ACCEPT', 'application/json');
        $client->setServerParameter('HTTP_CONTENT_TYPE', 'application/json');

        $client->request(
            method: 'POST',
            uri: '/api/appointments/' . $appointment->getId() .'/cancellation',
            content: \json_encode([
                'referenceNumber' => 'E4N6ST',
                'lastName' => 'SMITH',
                'reason' => 'I booked another one earlier.',
            ]),
        );

        self::assertResponseIsSuccessful();

        $payload = \json_decode($client->getResponse()->getContent(), flags: \JSON_OBJECT_AS_ARRAY | \JSON_THROW_ON_ERROR);

        /** @var DataCollector $dbCollector */
        $dbCollector = $client->getProfile()->getCollector('db');

        self::assertLessThanOrEqual(10, $dbCollector->getQueryCount());
        self::assertArrayHasKey('cancelledAt', $payload);
        self::assertSame($payload['cancelledAt'], $appointment->getCancelledAt()?->format('c'));

        /*
        $this->browser()
            ->withProfiling()
            ->post(
                url: \sprintf('/api/appointments/%s/cancellation', $appointment->getId()),
                options: HttpOptions::json([
                    'referenceNumber' => 'E4N6ST',
                    'lastName' => 'SMITH',
                    'reason' => 'I booked another one earlier.',
                ]),
            )
            ->assertSuccessful()
            ->assertJson()
            ->assertJsonMatches('cancelledAt', $appointment->getCancelledAt()->format('c'))
            ->use(function (MailerComponent $component): void {
                $component->assertSentEmailCount(1);
            })
        ;
        */

        // Assert: check the appointment is cancelled in the database
        MedicalAppointmentFactory::assert()->count(1, [
            'id' => $appointment->getId(),
            'cancelledAt' => $appointment->getCancelledAt(),
            'cancellationReason' => 'I booked another one earlier.',
        ]);
    }
}