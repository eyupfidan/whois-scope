<?php

namespace App\Domain\Whois\ValueObjects;

final readonly class DomainName
{
    private function __construct(private string $value) {}

    public static function fromValidated(string $ascii): self
    {
        return new self($ascii);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
