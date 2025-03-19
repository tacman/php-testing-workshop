<?php

declare(strict_types=1);

namespace App\Entity;

enum AgendaSlotStatus: string
{
    case BLOCKED = 'blocked';
    case BOOKED = 'booked';
    case OPEN = 'open';

    public function getLabel(): string
    {
        return \ucfirst($this->value);
    }
}
