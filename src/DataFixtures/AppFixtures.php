<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Agenda;
use App\Entity\HealthSpecialist;
use App\Entity\MedicalSpecialty;
use App\Service\GenerateAgendaCalendar;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator as Faker;

final class AppFixtures extends Fixture
{
    private static ?Faker $faker = null;

    public function __construct(
        private readonly GenerateAgendaCalendar $generateAgendaCalendar,
    ) {
    }

    public function load(ObjectManager $manager): void
    {
        $faker = self::faker();

        $specialist1 = new HealthSpecialist('Linda', 'Helmann', MedicalSpecialty::GYNECOLOGIST);
        $specialist1->setIntroduction($faker->text(maxNbChars: 164));
        $specialist1->setBiography($faker->paragraphs($faker->numberBetween(2, 6), asText: true));
        $specialist1->setProfilePictureUrl('https://images.unsplash.com/photo-1494790108377-be9c29b29330?fit=facearea&facepad=2&w=256&h=256&q=80');

        $specialist2 = new HealthSpecialist('James', 'Clark', MedicalSpecialty::OPHTHALMOLOGIST);
        $specialist2->setIntroduction($faker->text(maxNbChars: 164));
        $specialist2->setBiography($faker->paragraphs($faker->numberBetween(2, 6), asText: true));
        $specialist2->setProfilePictureUrl('https://images.unsplash.com/photo-1519244703995-f4e0f30006d5?fit=facearea&facepad=2&w=256&h=256&q=80');

        $specialist3 = new HealthSpecialist('Vincent', 'Dries', MedicalSpecialty::GYNECOLOGIST);
        $specialist3->setIntroduction($faker->text(maxNbChars: 164));
        $specialist3->setBiography($faker->paragraphs($faker->numberBetween(2, 6), asText: true));
        $specialist3->setProfilePictureUrl('https://images.unsplash.com/photo-1517841905240-472988babdf9');

        $specialist4 = new HealthSpecialist('Cassidy', 'McBeal', MedicalSpecialty::DERMATOLOGIST);
        $specialist4->setIntroduction($faker->text(maxNbChars: 164));
        $specialist4->setBiography($faker->paragraphs($faker->numberBetween(2, 6), asText: true));
        $specialist4->setProfilePictureUrl('https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?fit=facearea&facepad=2&w=256&h=256&q=80');

        $specialist5 = new HealthSpecialist('Courtney', 'Henry', MedicalSpecialty::ANESTHETIST);
        $specialist5->setIntroduction($faker->text(maxNbChars: 164));
        $specialist5->setBiography($faker->paragraphs($faker->numberBetween(2, 6), asText: true));
        $specialist5->setProfilePictureUrl('https://images.unsplash.com/photo-1438761681033-6461ffad8d80?fit=facearea&facepad=2&w=256&h=256&q=80');

        $specialist6 = new HealthSpecialist('Tom', 'Cook', MedicalSpecialty::DENTIST);
        $specialist6->setIntroduction($faker->text(maxNbChars: 164));
        $specialist6->setBiography($faker->paragraphs($faker->numberBetween(2, 6), asText: true));
        $specialist6->setProfilePictureUrl('https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?fit=facearea&facepad=2&w=256&h=256&q=80');

        $manager->persist($specialist1);
        $manager->persist($specialist2);
        $manager->persist($specialist3);
        $manager->persist($specialist4);
        $manager->persist($specialist5);
        $manager->persist($specialist6);

        $agenda1 = new Agenda($specialist1);
        $agenda2 = new Agenda($specialist2);
        $agenda3 = new Agenda($specialist3);
        $agenda4 = new Agenda($specialist4);
        $agenda5 = new Agenda($specialist5);
        $agenda6 = new Agenda($specialist6);

        $manager->persist($agenda1);
        $manager->persist($agenda2);
        $manager->persist($agenda3);
        $manager->persist($agenda4);
        $manager->persist($agenda5);
        $manager->persist($agenda6);

        $manager->flush();

        $this->generateAgendaCalendar->generateCalendarFor($agenda1, '+5 days', '+2 months', saturday: false, sunday: false);
        $this->generateAgendaCalendar->generateCalendarFor($agenda2, 'today', '+1 months', wednesday: false, thursday: false);
        $this->generateAgendaCalendar->generateCalendarFor($agenda3, '+4 months', '+4 months', monday: false, sunday: false);
        $this->generateAgendaCalendar->generateCalendarFor($agenda4, 'today', '+2 months', monday: false, saturday: false, sunday: false);
        $this->generateAgendaCalendar->generateCalendarFor($agenda5, 'today', '+1 months', tuesday: false, wednesday: false, sunday: false);
        $this->generateAgendaCalendar->generateCalendarFor($agenda6, 'today', '+4 months', friday: false, sunday: false);

        $manager->flush();

        $agenda1->publish();
        $agenda2->publish();
        $agenda3->unpublish();
        $agenda4->unpublish();
        $agenda5->publish();
        $agenda6->publish();

        $manager->flush();
    }

    private static function faker(): Faker
    {
        if (self::$faker === null) {
            self::$faker = Factory::create();
        }

        return self::$faker;
    }
}
