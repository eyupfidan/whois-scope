<?php

namespace App\Contracts;

use App\DTOs\WhoisLookupResult;

interface WhoisClient
{
    public function lookup(string $domain): WhoisLookupResult;

    public function raw(string $domain): string;
}
