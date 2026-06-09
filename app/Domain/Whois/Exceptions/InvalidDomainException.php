<?php

namespace App\Domain\Whois\Exceptions;

use Exception;

class InvalidDomainException extends Exception implements UserFacingException
{
    public function __construct(private readonly string $domain)
    {
        parent::__construct("Invalid domain: {$domain}", 422);
    }

    public function errorCode(): string
    {
        return 'invalid_domain';
    }

    public function userMessage(): string
    {
        return 'The domain name you entered is not valid. Please check the format and try again.';
    }
}
