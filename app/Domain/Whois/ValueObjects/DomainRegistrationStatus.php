<?php

namespace App\Domain\Whois\ValueObjects;

enum DomainRegistrationStatus: string
{
    case Registered = 'registered';
    case Available = 'available';
    case Unknown = 'unknown';
}
