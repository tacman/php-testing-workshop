<?php

declare(strict_types=1);

namespace App\Model;

use Symfony\Component\Validator\Constraints as Assert;

final class MedicalAppointmentBooking
{
    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    private ?string $firstName = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 128)]
    private ?string $lastName = null;

    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 8, max: 32)]
    #[Assert\Regex(
        pattern: '/^\+?[0-9]+$/',
        message: 'The phone number can only contain digits and a leading plus sign.',
    )]
    private ?string $phone = null;

    public function __construct(
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $email = null,
        ?string $phone = null,
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->phone = $phone;
    }

    public function getFirstName(): string
    {
        return (string) $this->firstName;
    }

    public function setFirstName(?string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return (string) $this->lastName;
    }

    public function setLastName(?string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return (string) $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): string
    {
        return (string) $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }
}
