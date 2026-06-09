<?php

namespace App\Domain\Whois\Exceptions;

use Exception;
use Throwable;

class WhoisParseException extends Exception implements UserFacingException
{
    public function __construct(private readonly string $domain, ?Throwable $previous = null)
    {
        parent::__construct("WHOIS response parse failed for: {$domain}", 502, $previous);
    }

    public function errorCode(): string
    {
        return 'parse_failed';
    }

    public function userMessage(): string
    {
        return 'Whois data for this domain could not be processed. Please try again or use a different format.';
    }
}
