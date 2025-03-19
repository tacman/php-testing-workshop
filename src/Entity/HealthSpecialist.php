<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DoctrineHealthSpecialistRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Stringable;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DoctrineHealthSpecialistRepository::class)]
class HealthSpecialist implements Stringable
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    #[Groups(['medical_appointment:read'])]
    private string $id;

    #[ORM\Column]
    private readonly string $firstName;

    #[ORM\Column]
    private readonly string $lastName;

    #[ORM\Column(enumType: MedicalSpecialty::class)]
    private MedicalSpecialty $specialty;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $introduction = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $biography = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $profilePictureUrl = null;

    public function __construct(
        string $firstName,
        string $lastName,
        MedicalSpecialty $specialty,
    ) {
        $this->id = (string) Uuid::v4();
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->specialty = $specialty;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    #[Groups(['medical_appointment:read'])]
    public function getFullName(): string
    {
        return $this->firstName . ' ' . $this->lastName;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getSpecialty(): MedicalSpecialty
    {
        return $this->specialty;
    }

    public function getIntroduction(): ?string
    {
        return $this->introduction;
    }

    public function setIntroduction(?string $introduction): void
    {
        $this->introduction = $introduction;
    }

    public function getBiography(): ?string
    {
        return $this->biography;
    }

    public function setBiography(?string $biography): void
    {
        $this->biography = $biography;
    }

    public function getProfilePictureUrl(): string
    {
        return (string) $this->profilePictureUrl;
    }

    public function setProfilePictureUrl(?string $profilePictureUrl): void
    {
        $this->profilePictureUrl = $profilePictureUrl;
    }
}
