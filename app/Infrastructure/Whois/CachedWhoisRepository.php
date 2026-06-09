<?php

namespace App\Infrastructure\Whois;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\ValueObjects\DomainName;
use Illuminate\Support\Facades\Cache;

class CachedWhoisRepository implements WhoisRepositoryInterface
{
    public function __construct(
        private readonly PhpWhoisRepository $repository,
    ) {}

    public function lookup(DomainName $domain): WhoisRecord
    {
        if (! config('whois.cache_enabled', true)) {
            return $this->repository->lookup($domain);
        }

        $key = $this->cacheKey($domain);

        /** @var array<string, mixed>|null $cached */
        $cached = Cache::get($key);

        if (is_array($cached)) {
            return WhoisRecord::fromCacheArray($cached);
        }

        $record = $this->repository->lookup($domain);

        Cache::put($key, $record->toCacheArray(), config('whois.cache_ttl'));

        return $record;
    }

    private function cacheKey(DomainName $domain): string
    {
        return 'whois:'.hash('xxh128', $domain->toString());
    }
}
