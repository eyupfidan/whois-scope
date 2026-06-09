<?php

namespace App\Domain\Whois\Exceptions;

interface UserFacingException
{
    public function errorCode(): string;

    public function userMessage(): string;
}
