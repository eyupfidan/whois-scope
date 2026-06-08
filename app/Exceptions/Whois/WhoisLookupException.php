<?php

namespace App\Exceptions\Whois;

use Exception;
use Throwable;

class WhoisLookupException extends Exception
{
    public function __construct(string $domain, ?Throwable $previous = null)
    {
        parent::__construct("WHOIS lookup failed for: {$domain}", 502, $previous);
    }
}
