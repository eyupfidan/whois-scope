<?php

namespace App\Domain\Whois\ValueObjects;

enum LookupFormat: string
{
    case Full = 'full';
    case Summary = 'summary';

    public static function fromString(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new \InvalidArgumentException("Invalid format: {$value}. Allowed: full, summary.");
    }
}
