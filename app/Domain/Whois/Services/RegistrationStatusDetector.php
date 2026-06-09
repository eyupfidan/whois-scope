<?php

namespace App\Domain\Whois\Services;

use App\Domain\Whois\ValueObjects\DomainRegistrationStatus;

class RegistrationStatusDetector
{
    /**
     * @param  list<string>  $nameServers
     * @param  list<string>  $states
     */
    public function detect(
        string $raw,
        ?string $registrar,
        ?string $createdAt,
        ?string $expiresAt,
        array $nameServers,
        array $states,
    ): DomainRegistrationStatus {
        if ($registrar || $createdAt || $expiresAt || $nameServers !== [] || $states !== []) {
            return DomainRegistrationStatus::Registered;
        }

        $rawLower = strtolower($raw);

        foreach ($this->availablePatterns() as $pattern) {
            if (str_contains($rawLower, $pattern)) {
                return DomainRegistrationStatus::Available;
            }
        }

        if (trim($raw) === '') {
            return DomainRegistrationStatus::Available;
        }

        return DomainRegistrationStatus::Unknown;
    }

    /**
     * @return list<string>
     */
    private function availablePatterns(): array
    {
        return [
            'no match for',
            'no match',
            'not found',
            'no data found',
            'no entries found',
            'no object found',
            'not registered',
            'domain not found',
            'no matching record',
            'status: free',
            'available for registration',
            'is available for registration',
            'domain you requested is not registered',
            'nothing found',
            'no such domain',
        ];
    }
}
