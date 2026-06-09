<?php

namespace App\Domain\Whois\Entities;

use App\Domain\Whois\ValueObjects\DomainName;

final readonly class WhoisRecord
{
    /**
     * @param  list<string>  $nameServers
     * @param  list<string>  $states
     */
    public function __construct(
        public DomainName $domain,
        public string $whoisServer,
        public ?string $registrar,
        public ?string $owner,
        public ?string $createdAt,
        public ?string $updatedAt,
        public ?string $expiresAt,
        public array $nameServers,
        public array $states,
        public ?string $dnssec,
        public string $raw,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toSummary(): array
    {
        return [
            'domain' => $this->domain->toString(),
            'registrar' => $this->registrar,
            'created_at' => $this->createdAt,
            'expires_at' => $this->expiresAt,
            'states' => $this->states,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toFull(): array
    {
        return [
            'domain' => $this->domain->toString(),
            'whois_server' => $this->whoisServer,
            'registrar' => $this->registrar,
            'owner' => $this->owner,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'expires_at' => $this->expiresAt,
            'name_servers' => $this->nameServers,
            'states' => $this->states,
            'dnssec' => $this->dnssec,
            'raw' => $this->raw,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function toCacheArray(): array
    {
        return [
            'domain' => $this->domain->toString(),
            'whois_server' => $this->whoisServer,
            'registrar' => $this->registrar,
            'owner' => $this->owner,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'expires_at' => $this->expiresAt,
            'name_servers' => $this->nameServers,
            'states' => $this->states,
            'dnssec' => $this->dnssec,
            'raw' => $this->raw,
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromCacheArray(array $data): self
    {
        return new self(
            domain: DomainName::fromValidated((string) $data['domain']),
            whoisServer: (string) ($data['whois_server'] ?? ''),
            registrar: $data['registrar'] ?? null,
            owner: $data['owner'] ?? null,
            createdAt: $data['created_at'] ?? null,
            updatedAt: $data['updated_at'] ?? null,
            expiresAt: $data['expires_at'] ?? null,
            nameServers: array_values($data['name_servers'] ?? []),
            states: array_values($data['states'] ?? []),
            dnssec: $data['dnssec'] ?? null,
            raw: (string) ($data['raw'] ?? ''),
        );
    }
}
