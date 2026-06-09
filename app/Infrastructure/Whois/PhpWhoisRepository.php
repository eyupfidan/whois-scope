<?php

namespace App\Infrastructure\Whois;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Exceptions\WhoisLookupException;
use App\Domain\Whois\Exceptions\WhoisParseException;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\Services\RegistrationStatusDetector;
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

    public function __construct(
        private readonly RegistrationStatusDetector $registrationStatusDetector,
    ) {
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
        } catch (\Throwable $exception) {
            throw new WhoisParseException($domainValue, $exception);
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
            return $this->buildRecord(
                domain: $domain,
                raw: $raw,
                whoisServer: '',
                registrar: null,
                owner: null,
                createdAt: null,
                updatedAt: null,
                expiresAt: null,
                nameServers: [],
                states: [],
                dnssec: null,
            );
        }

        $registrar = $info->registrar ?: null;
        $owner = $info->owner ?: null;
        $createdAt = $this->formatTimestamp($info->creationDate);
        $updatedAt = $this->formatTimestamp($info->updatedDate);
        $expiresAt = $this->formatTimestamp($info->expirationDate);
        $nameServers = array_values($info->nameServers);
        $states = array_values($info->states);
        $dnssec = $info->dnssec ?: null;

        return $this->buildRecord(
            domain: DomainName::fromValidated($info->domainName ?: $domain->toString()),
            raw: $raw,
            whoisServer: $info->whoisServer,
            registrar: $registrar,
            owner: $owner,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            expiresAt: $expiresAt,
            nameServers: $nameServers,
            states: $states,
            dnssec: $dnssec,
        );
    }

    /**
     * @param  list<string>  $nameServers
     * @param  list<string>  $states
     */
    private function buildRecord(
        DomainName $domain,
        string $raw,
        string $whoisServer,
        ?string $registrar,
        ?string $owner,
        ?string $createdAt,
        ?string $updatedAt,
        ?string $expiresAt,
        array $nameServers,
        array $states,
        ?string $dnssec,
    ): WhoisRecord {
        $registrationStatus = $this->registrationStatusDetector->detect(
            raw: $raw,
            registrar: $registrar,
            createdAt: $createdAt,
            expiresAt: $expiresAt,
            nameServers: $nameServers,
            states: $states,
        );

        return new WhoisRecord(
            domain: $domain,
            registrationStatus: $registrationStatus,
            whoisServer: $whoisServer,
            registrar: $registrar,
            owner: $owner,
            createdAt: $createdAt,
            updatedAt: $updatedAt,
            expiresAt: $expiresAt,
            nameServers: $nameServers,
            states: $states,
            dnssec: $dnssec,
            raw: $raw,
        );
    }

    private function formatTimestamp(mixed $timestamp): ?string
    {
        if ($timestamp === null || $timestamp === '' || $timestamp === 0 || $timestamp === '0') {
            return null;
        }

        if (is_numeric($timestamp)) {
            $value = (int) $timestamp;

            if ($value <= 0) {
                return null;
            }

            return Carbon::createFromTimestamp($value)->toIso8601String();
        }

        if (is_string($timestamp)) {
            try {
                return Carbon::parse($timestamp)->toIso8601String();
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }
}
