<?php

namespace App\Domain\Whois\Exceptions;

use Exception;
use Throwable;

class WhoisLookupException extends Exception implements UserFacingException
{
    public function __construct(private readonly string $domain, ?Throwable $previous = null)
    {
        parent::__construct("WHOIS lookup failed for: {$domain}", 502, $previous);
    }

    public function errorCode(): string
    {
        return 'lookup_failed';
    }

    public function userMessage(): string
    {
        return 'We could not retrieve whois information for this domain. The whois server may be unavailable — please try again later.';
    }
}
