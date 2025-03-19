<?php

declare(strict_types=1);

namespace App\Entity;

enum MedicalSpecialty: string
{
    case ANESTHETIST = 'anesthetist';
    case CARDIOLOGIST = 'cardiologist';
    case DENTIST = 'dentist';
    case DERMATOLOGIST = 'dermatologist';
    case GYNECOLOGIST = 'gynecologist';
    case NEUROLOGIST = 'neurologist';
    case OPHTHALMOLOGIST = 'ophthalmologist';
    case ORTHOPEDIST = 'orthopedist';
    case PSYCHIATRIST = 'psychiatrist';
    case RADIOLOGIST = 'radiologist';

    public function getLabel(): string
    {
        return \ucfirst($this->value);
    }
}
