<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\DoctrineAgendaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: DoctrineAgendaRepository::class)]
class Agenda
{
    #[ORM\Id]
    #[ORM\Column(type: Types::GUID)]
    private readonly string $id;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private readonly HealthSpecialist $owner;

    #[ORM\Column(nullable: false)]
    private bool $isPublished = false;

    public function __construct(HealthSpecialist $owner)
    {
        $this->id = (string) Uuid::v4();
        $this->owner = $owner;
    }

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }

    public function getOwner(): HealthSpecialist
    {
        return $this->owner;
    }

    public function isPublished(): bool
    {
        return $this->isPublished;
    }

    public function publish(): void
    {
        $this->isPublished = true;
    }

    public function unpublish(): void
    {
        $this->isPublished = false;
    }
}
