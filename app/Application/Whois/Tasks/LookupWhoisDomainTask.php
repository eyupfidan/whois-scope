<?php

namespace App\Application\Whois\Tasks;

use App\Application\Whois\Services\DomainWhoisLookupService;

final class LookupWhoisDomainTask
{
    /** @return array<string, mixed> */
    public static function handle(string $domainInput): array
    {
        return app(DomainWhoisLookupService::class)
            ->lookup($domainInput)
            ->toPayload();
    }
}
