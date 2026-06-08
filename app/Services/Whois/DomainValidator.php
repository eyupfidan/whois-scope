<?php

namespace App\Services\Whois;

use App\Exceptions\Whois\InvalidDomainException;
use Iodev\Whois\Helpers\DomainHelper;

class DomainValidator
{
    public function normalize(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain) ?? $domain;
        $domain = preg_replace('#/.*$#', '', $domain) ?? $domain;

        if (str_starts_with($domain, 'www.')) {
            $domain = substr($domain, 4);
        }

        return $domain;
    }

    /**
     * @throws InvalidDomainException
     */
    public function validate(string $domain): string
    {
        $domain = $this->normalize($domain);
        $ascii = DomainHelper::toAscii($domain);

        if ($ascii === '' || ! str_contains($ascii, '.')) {
            throw new InvalidDomainException($domain);
        }

        if (! filter_var($ascii, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            throw new InvalidDomainException($domain);
        }

        return $ascii;
    }
}
