<?php

namespace Tests\Unit;

use App\Domain\Whois\Services\RegistrationStatusDetector;
use App\Domain\Whois\ValueObjects\DomainRegistrationStatus;
use PHPUnit\Framework\TestCase;

class RegistrationStatusDetectorTest extends TestCase
{
    private RegistrationStatusDetector $detector;

    protected function setUp(): void
    {
        parent::setUp();
        $this->detector = new RegistrationStatusDetector;
    }

    public function test_detects_available_domain_from_raw_text(): void
    {
        $status = $this->detector->detect(
            raw: 'No match for "EXAMPLE.COM".',
            registrar: null,
            createdAt: null,
            expiresAt: null,
            nameServers: [],
            states: [],
        );

        $this->assertSame(DomainRegistrationStatus::Available, $status);
    }

    public function test_detects_registered_domain_with_registrar(): void
    {
        $status = $this->detector->detect(
            raw: 'Domain Name: EXAMPLE.COM',
            registrar: 'Example Registrar',
            createdAt: '2020-01-01T00:00:00+00:00',
            expiresAt: null,
            nameServers: [],
            states: [],
        );

        $this->assertSame(DomainRegistrationStatus::Registered, $status);
    }
}
