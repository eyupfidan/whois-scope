<?php

namespace Tests\Feature;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\ValueObjects\DomainName;
use Tests\TestCase;

class WhoisApiTest extends TestCase
{
    public function test_single_summary_returns_minimal_fields(): void
    {
        $this->mock(WhoisRepositoryInterface::class, function ($mock): void {
            $mock->shouldReceive('lookup')
                ->once()
                ->andReturn($this->sampleRecord());
        });

        $response = $this->getJson('/api/v1/whois/example.com?format=summary');

        $response
            ->assertOk()
            ->assertJsonPath('data.domain', 'example.com')
            ->assertJsonPath('data.registrar', 'Example Registrar')
            ->assertJsonPath('data.expires_at', '2025-08-13T04:00:00+00:00')
            ->assertJsonMissingPath('data.raw')
            ->assertJsonMissingPath('data.whois_server');
    }

    public function test_single_full_returns_all_fields(): void
    {
        $this->mock(WhoisRepositoryInterface::class, function ($mock): void {
            $mock->shouldReceive('lookup')
                ->once()
                ->andReturn($this->sampleRecord());
        });

        $response = $this->getJson('/api/v1/whois/example.com?format=full');

        $response
            ->assertOk()
            ->assertJsonPath('data.domain', 'example.com')
            ->assertJsonPath('data.whois_server', 'whois.iana.org')
            ->assertJsonPath('data.raw', 'Domain Name: EXAMPLE.COM')
            ->assertJsonPath('data.name_servers.0', 'a.iana-servers.net');
    }

    public function test_single_defaults_to_summary_format(): void
    {
        $this->mock(WhoisRepositoryInterface::class, function ($mock): void {
            $mock->shouldReceive('lookup')
                ->once()
                ->andReturn($this->sampleRecord());
        });

        $response = $this->getJson('/api/v1/whois/example.com');

        $response
            ->assertOk()
            ->assertJsonMissingPath('data.raw');
    }

    public function test_bulk_returns_results_per_domain(): void
    {
        $this->mock(WhoisRepositoryInterface::class, function ($mock): void {
            $mock->shouldReceive('lookup')
                ->once()
                ->withArgs(fn (DomainName $domain) => $domain->toString() === 'example.com')
                ->andReturn($this->sampleRecord());
        });

        $response = $this->postJson('/api/v1/whois/bulk', [
            'domains' => ['example.com', 'not-a-valid-domain'],
            'format' => 'summary',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('format', 'summary')
            ->assertJsonPath('results.0.status', 'success')
            ->assertJsonPath('results.0.data.domain', 'example.com')
            ->assertJsonPath('results.1.status', 'error')
            ->assertJsonPath('results.1.code', 'invalid_domain');
    }

    public function test_bulk_full_format_includes_raw_text(): void
    {
        $this->mock(WhoisRepositoryInterface::class, function ($mock): void {
            $mock->shouldReceive('lookup')
                ->once()
                ->andReturn($this->sampleRecord());
        });

        $response = $this->postJson('/api/v1/whois/bulk', [
            'domains' => ['example.com'],
            'format' => 'full',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('format', 'full')
            ->assertJsonPath('results.0.data.raw', 'Domain Name: EXAMPLE.COM');
    }

    public function test_invalid_domain_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/whois/not-a-valid-domain');

        $response
            ->assertUnprocessable()
            ->assertJsonPath('code', 'invalid_domain')
            ->assertJsonPath('message', 'The domain name you entered is not valid. Please check the format and try again.');
    }

    private function sampleRecord(string $domain = 'example.com'): WhoisRecord
    {
        return new WhoisRecord(
            domain: DomainName::fromValidated($domain),
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
