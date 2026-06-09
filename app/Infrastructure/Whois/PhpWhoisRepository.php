<?php

namespace App\Infrastructure\Whois;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Exceptions\WhoisLookupException;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\ValueObjects\DomainName;
use Carbon\Carbon;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory;
use Iodev\Whois\Modules\Tld\TldInfo;
use Iodev\Whois\Modules\Tld\TldServer;
use Iodev\Whois\Whois;

class PhpWhoisRepository implements WhoisRepositoryInterface
{
    private Whois $whois;

    public function __construct()
    {
        $this->whois = Factory::get()->createWhois();
        $this->registerCustomServers();
    }

    public function lookup(DomainName $domain): WhoisRecord
    {
        $domainValue = $domain->toString();

        try {
            $response = $this->whois->lookupDomain($domainValue);
            $info = $this->whois->loadDomainInfo($domainValue);

            return $this->mapRecord($domain, $response->text, $info);
        } catch (ConnectionException|ServerMismatchException|WhoisException $exception) {
            throw new WhoisLookupException($domainValue, $exception);
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

    private function mapRecord(DomainName $domain, string $raw, ?TldInfo $info): WhoisRecord
    {
        if ($info === null) {
            return new WhoisRecord(
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

        return new WhoisRecord(
            domain: DomainName::fromValidated($info->domainName ?: $domain->toString()),
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
