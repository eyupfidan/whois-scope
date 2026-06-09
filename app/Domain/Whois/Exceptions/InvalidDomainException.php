<?php

namespace App\Domain\Whois\Exceptions;

use Exception;

class InvalidDomainException extends Exception
{
    public function __construct(string $domain)
    {
        parent::__construct("Invalid domain: {$domain}", 422);
    }
}
