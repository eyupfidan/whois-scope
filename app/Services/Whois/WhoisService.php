<?php

namespace App\Services\Whois;

use App\Contracts\WhoisClient;
use App\DTOs\WhoisLookupResult;

class WhoisService
{
    public function __construct(
        private readonly WhoisClient $client,
        private readonly DomainValidator $validator,
    ) {}

    public function lookup(string $domain): WhoisLookupResult
    {
        $domain = $this->validator->validate($domain);

        return $this->client->lookup($domain);
    }

    public function raw(string $domain): string
    {
        $domain = $this->validator->validate($domain);

        return $this->client->raw($domain);
    }
}
