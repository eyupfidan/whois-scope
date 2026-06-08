<?php

namespace App\Services\Whois;

use App\Contracts\WhoisClient;
use App\DTOs\WhoisLookupResult;
use App\Exceptions\Whois\WhoisLookupException;
use Carbon\Carbon;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;
use Iodev\Whois\Modules\Tld\TldInfo;
use Iodev\Whois\Modules\Tld\TldServer;
use Iodev\Whois\Whois;

class PhpWhoisClient implements WhoisClient
{
    private Whois $whois;

    public function __construct()
    {
        $this->whois = Factory::get()->createWhois();
        $this->registerCustomServers();
    }

    public function lookup(string $domain): WhoisLookupResult
    {
        try {
            $response = $this->whois->lookupDomain($domain);
            $info = $this->whois->loadDomainInfo($domain);

            return $this->mapResult($domain, $response->text, $info);
        } catch (ConnectionException|ServerMismatchException|WhoisException $exception) {
            throw new WhoisLookupException($domain, $exception);
        }
    }

    public function raw(string $domain): string
    {
        try {
            return $this->whois->lookupDomain($domain)->text;
        } catch (ConnectionException|ServerMismatchException|WhoisException $exception) {
            throw new WhoisLookupException($domain, $exception);
        }
    }

    private function registerCustomServers(): void
    {
        $servers = config('whois.custom_servers', []);

        if ($servers === []) {
            return;
        }

        $this->whois->getTldModule()->addServers(
            TldServer::fromDataList($servers)
        );
    }

    private function mapResult(string $domain, string $raw, ?TldInfo $info): WhoisLookupResult
    {
        if ($info === null) {
            return new WhoisLookupResult(
                domain: $domain,
                whoisServer: '',
                registrar: null,
                owner: null,
                createdAt: null,
                updatedAt: null,
                expiresAt: null,
                nameServers: [],
                states: [],
                dnssec: null,
                raw: $raw,
            );
        }

        return new WhoisLookupResult(
            domain: $info->domainName ?: $domain,
            whoisServer: $info->whoisServer,
            registrar: $info->registrar ?: null,
            owner: $info->owner ?: null,
            createdAt: $this->formatTimestamp($info->creationDate),
            updatedAt: $this->formatTimestamp($info->updatedDate),
            expiresAt: $this->formatTimestamp($info->expirationDate),
            nameServers: array_values($info->nameServers),
            states: array_values($info->states),
            dnssec: $info->dnssec ?: null,
            raw: $raw,
        );
    }

    private function formatTimestamp(int $timestamp): ?string
    {
        if ($timestamp <= 0) {
            return null;
        }

        return Carbon::createFromTimestamp($timestamp)->toIso8601String();
    }
}
