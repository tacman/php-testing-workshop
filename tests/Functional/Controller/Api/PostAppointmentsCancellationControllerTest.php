<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\Api;

use App\Factory\MedicalAppointmentFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Zenstruck\Browser\HttpOptions;
use Zenstruck\Browser\Json;
use Zenstruck\Browser\KernelBrowser;
use Zenstruck\Browser\Test\HasBrowser;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;
use Zenstruck\Mailer\Test\Bridge\Zenstruck\Browser\MailerComponent;
use Zenstruck\Mailer\Test\InteractsWithMailer;

final class PostAppointmentsCancellationControllerTest extends WebTestCase
{
    use Factories;
    use InteractsWithMailer;
    use ResetDatabase;

    use HasBrowser {
        browser as baseKernelBrowser;
    }

    public function testCancelScheduledUpcomingAppointment(): void
    {
        $appointment = MedicalAppointmentFactory::new()
            ->scheduled('E4N6ST')
            ->tomorrowAt('10:00')
            ->forPatient('John', 'Smith')
            ->create(['email' => 'user@example.com']);

        self::ensureKernelShutdown();

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
            ->assertJsonMatches('[referenceNumber,patient]', ['E4N6ST', 'John Smith'])
            ->use(function (Json $json): void {
                $json->assertHas('referenceNumber');
            })
            ->use(function (MailerComponent $component): void {
                $component->assertSentEmailCount(1);
                $component->assertEmailSentTo('user@example.com', 'Your appointment has been cancelled');
            })
        ;

        // Assert: check the appointment is cancelled in the database
        MedicalAppointmentFactory::assert()->count(1, [
            'id' => $appointment->getId(),
            'cancelledAt' => $appointment->getCancelledAt(),
            'cancellationReason' => 'I booked another one earlier.',
        ]);
    }

    protected function browser(): KernelBrowser
    {
        return $this->baseKernelBrowser()
            ->interceptRedirects() // always intercept redirects
            ->throwExceptions() // always throw exceptions
            ;
    }
}