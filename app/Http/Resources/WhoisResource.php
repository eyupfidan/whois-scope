<?php

namespace App\Http\Resources;

use App\DTOs\WhoisLookupResult;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WhoisLookupResult */
class WhoisResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'domain' => $this->domain,
            'whois_server' => $this->whoisServer,
            'registrar' => $this->registrar,
            'owner' => $this->owner,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'expires_at' => $this->expiresAt,
            'name_servers' => $this->nameServers,
            'states' => $this->states,
            'dnssec' => $this->dnssec,
        ];
    }
}
