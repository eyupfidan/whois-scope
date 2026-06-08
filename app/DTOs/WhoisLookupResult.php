<?php

namespace App\DTOs;

readonly class WhoisLookupResult
{
    /**
     * @param  list<string>  $nameServers
     * @param  list<string>  $states
     */
    public function __construct(
        public string $domain,
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
}
