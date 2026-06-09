<?php

namespace App\Infrastructure\Whois;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\Services\RegistrationStatusDetector;
use App\Domain\Whois\ValueObjects\DomainName;
use App\Domain\Whois\ValueObjects\DomainRegistrationStatus;
use Illuminate\Support\Facades\Cache;

class CachedWhoisRepository implements WhoisRepositoryInterface
{
    private const CACHE_KEY_VERSION = 'v2';

    public function __construct(
        private readonly PhpWhoisRepository $repository,
        private readonly RegistrationStatusDetector $registrationStatusDetector,
    ) {}

    public function lookup(DomainName $domain): WhoisRecord
    {
        if (! config('whois.cache_enabled', true)) {
            return $this->repository->lookup($domain);
        }

        $cached = $this->findCached($domain);

        if ($cached !== null) {
            return $cached;
        }

        $record = $this->repository->lookup($domain);

        Cache::put($this->cacheKey($domain), $record->toCacheArray(), config('whois.cache_ttl'));

        return $record;
    }

    public function findCached(DomainName $domain): ?WhoisRecord
    {
        if (! config('whois.cache_enabled', true)) {
            return null;
        }

        $key = $this->cacheKey($domain);

        /** @var array<string, mixed>|null $cached */
        $cached = Cache::get($key);

        if (! is_array($cached)) {
            return null;
        }

        $record = WhoisRecord::fromCacheArray($cached);
        $record = $this->reconcileRegistrationStatus($record);

        if (($cached['registration_status'] ?? null) !== $record->registrationStatus->value) {
            Cache::put($key, $record->toCacheArray(), config('whois.cache_ttl'));
        }

        return $record;
    }

    private function reconcileRegistrationStatus(WhoisRecord $record): WhoisRecord
    {
        if ($record->registrationStatus !== DomainRegistrationStatus::Unknown) {
            return $record;
        }

        $resolved = $this->registrationStatusDetector->detect(
            raw: $record->raw,
            registrar: $record->registrar,
            createdAt: $record->createdAt,
            expiresAt: $record->expiresAt,
            nameServers: $record->nameServers,
            states: $record->states,
        );

        if ($resolved === DomainRegistrationStatus::Unknown) {
            return $record;
        }

        return $record->withRegistrationStatus($resolved);
    }

    private function cacheKey(DomainName $domain): string
    {
        return 'whois:'.self::CACHE_KEY_VERSION.':'.hash('xxh128', $domain->toString());
    }
}
