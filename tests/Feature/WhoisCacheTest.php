<?php

namespace Tests\Feature;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Services\RegistrationStatusDetector;
use App\Domain\Whois\ValueObjects\DomainName;
use App\Domain\Whois\ValueObjects\DomainRegistrationStatus;
use App\Infrastructure\Whois\CachedWhoisRepository;
use App\Infrastructure\Whois\PhpWhoisRepository;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class WhoisCacheTest extends TestCase
{
    public function test_lookup_result_is_cached(): void
    {
        Cache::flush();

        $inner = Mockery::mock(PhpWhoisRepository::class);
        $inner->shouldReceive('lookup')
            ->once()
            ->andReturn($this->sampleRecord());

        $repository = new CachedWhoisRepository($inner, new RegistrationStatusDetector);
        $domain = DomainName::fromValidated('example.com');

        $repository->lookup($domain);
        $repository->lookup($domain);
    }

    public function test_cache_can_be_disabled(): void
    {
        config(['whois.cache_enabled' => false]);

        $inner = Mockery::mock(PhpWhoisRepository::class);
        $inner->shouldReceive('lookup')
            ->twice()
            ->andReturn($this->sampleRecord());

        $repository = new CachedWhoisRepository($inner, new RegistrationStatusDetector);
        $domain = DomainName::fromValidated('example.com');

        $repository->lookup($domain);
        $repository->lookup($domain);
    }

    public function test_legacy_cached_unknown_status_is_reconciled(): void
    {
        Cache::flush();

        $domain = DomainName::fromValidated('example.com');
        $key = 'whois:v2:'.hash('xxh128', $domain->toString());

        Cache::put($key, [
            'domain' => 'example.com',
            'registration_status' => 'unknown',
            'whois_server' => 'whois.iana.org',
            'registrar' => 'Example Registrar',
            'owner' => null,
            'created_at' => '1995-08-14T04:00:00+00:00',
            'updated_at' => '2024-08-14T07:01:38+00:00',
            'expires_at' => '2025-08-13T04:00:00+00:00',
            'name_servers' => ['a.iana-servers.net'],
            'states' => ['client delete prohibited'],
            'dnssec' => 'unsigned',
            'raw' => 'Domain Name: EXAMPLE.COM',
        ], 3600);

        $inner = Mockery::mock(PhpWhoisRepository::class);
        $inner->shouldNotReceive('lookup');

        $repository = new CachedWhoisRepository($inner, new RegistrationStatusDetector);
        $record = $repository->lookup($domain);

        $this->assertSame(DomainRegistrationStatus::Registered, $record->registrationStatus);
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
