<?php

namespace Tests\Feature;

use App\Contracts\WhoisClient;
use App\DTOs\WhoisLookupResult;
use Tests\TestCase;

class WhoisApiTest extends TestCase
{
    public function test_whois_lookup_returns_parsed_data(): void
    {
        $this->mock(WhoisClient::class, function ($mock): void {
            $mock->shouldReceive('lookup')
                ->once()
                ->with('example.com')
                ->andReturn(new WhoisLookupResult(
                    domain: 'example.com',
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
                ));
        });

        $response = $this->getJson('/api/v1/whois/example.com');

        $response
            ->assertOk()
            ->assertJsonPath('data.domain', 'example.com')
            ->assertJsonPath('data.registrar', 'Example Registrar')
            ->assertJsonPath('data.name_servers.0', 'a.iana-servers.net');
    }

    public function test_whois_raw_returns_text_response(): void
    {
        $this->mock(WhoisClient::class, function ($mock): void {
            $mock->shouldReceive('raw')
                ->once()
                ->with('example.com')
                ->andReturn('Domain Name: EXAMPLE.COM');
        });

        $response = $this->getJson('/api/v1/whois/example.com/raw');

        $response
            ->assertOk()
            ->assertJsonPath('domain', 'example.com')
            ->assertJsonPath('raw', 'Domain Name: EXAMPLE.COM');
    }

    public function test_invalid_domain_returns_validation_error(): void
    {
        $response = $this->getJson('/api/v1/whois/not-a-valid-domain');

        $response
            ->assertUnprocessable()
            ->assertJsonPath('message', 'Invalid domain: not-a-valid-domain');
    }
}
