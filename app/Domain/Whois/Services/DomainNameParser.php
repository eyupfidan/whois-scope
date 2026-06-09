<?php

namespace App\Domain\Whois\Services;

use App\Domain\Whois\Exceptions\InvalidDomainException;
use App\Domain\Whois\ValueObjects\DomainName;
use Iodev\Whois\Helpers\DomainHelper;

class DomainNameParser
{
    public function parse(string $input): DomainName
    {
        $normalized = $this->normalize($input);
        $ascii = DomainHelper::toAscii($normalized);

        if ($ascii === '' || ! str_contains($ascii, '.')) {
            throw new InvalidDomainException($normalized);
        }

        if (! filter_var($ascii, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new InvalidDomainException($normalized);
        }

        return DomainName::fromValidated($ascii);
    }

    private function normalize(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain) ?? $domain;
        $domain = preg_replace('#/.*$#', '', $domain) ?? $domain;

        if (str_starts_with($domain, 'www.')) {
            $domain = substr($domain, 4);
        }

        return $domain;
    }
}
