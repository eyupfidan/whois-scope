<?php

namespace App\Application\Whois\UseCases;

use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\Services\DomainNameParser;

class LookupWhoisUseCase
{
    public function __construct(
        private readonly WhoisRepositoryInterface $repository,
        private readonly DomainNameParser $domainNameParser,
    ) {}

    public function execute(string $domainInput): WhoisRecord
    {
        $domain = $this->domainNameParser->parse($domainInput);

        return $this->repository->lookup($domain);
    }
}
