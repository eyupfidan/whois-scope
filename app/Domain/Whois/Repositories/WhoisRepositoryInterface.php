<?php

namespace App\Domain\Whois\Repositories;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\ValueObjects\DomainName;

interface WhoisRepositoryInterface
{
    public function lookup(DomainName $domain): WhoisRecord;
}
