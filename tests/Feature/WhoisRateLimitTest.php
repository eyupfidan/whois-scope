<?php

namespace Tests\Feature;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\ValueObjects\DomainName;
use App\Domain\Whois\ValueObjects\DomainRegistrationStatus;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class WhoisRateLimitTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_single_lookup_is_rate_limited(): void
    {
        config(['whois.rate_limit' => 2]);

        $this->mock(WhoisRepositoryInterface::class, function ($mock): void {
            $mock->shouldReceive('lookup')
                ->times(2)
                ->andReturn($this->sampleRecord());
        });

        $this->getJson('/api/v1/whois/example.com')->assertOk();
        $this->getJson('/api/v1/whois/example.com')->assertOk();
        $this->getJson('/api/v1/whois/example.com')->assertTooManyRequests();
    }

    private function sampleRecord(): WhoisRecord
    {
        return new WhoisRecord(
            domain: DomainName::fromValidated('example.com'),
            registrationStatus: DomainRegistrationStatus::Registered,
            whoisServer: 'whois.iana.org',
            registrar: 'Example Registrar',
            owner: null,
            createdAt: '1995-08-14T04:00:00+00:00',
            updatedAt: '2024-08-14T07:01:38+00:00',
            expiresAt: '2025-08-13T04:00:00+00:00',
            nameServers: ['a.iana-servers.net'],
            states: ['client delete prohibited'],
            dnssec: 'unsigned',
            raw: 'Domain Name: EXAMPLE.COM',
        );
    }
}
