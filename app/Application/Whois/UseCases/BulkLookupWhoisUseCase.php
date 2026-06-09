<?php

namespace App\Application\Whois\UseCases;

use App\Application\Whois\DTOs\BulkWhoisItemResult;
use App\Domain\Whois\Exceptions\InvalidDomainException;
use App\Domain\Whois\Exceptions\WhoisLookupException;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\Services\DomainNameParser;

class BulkLookupWhoisUseCase
{
    public function __construct(
        private readonly WhoisRepositoryInterface $repository,
        private readonly DomainNameParser $domainNameParser,
    ) {}

    /**
     * @param  list<string>  $domainInputs
     * @return list<BulkWhoisItemResult>
     */
    public function execute(array $domainInputs): array
    {
        $results = [];

        foreach ($domainInputs as $domainInput) {
            $results[] = $this->lookupSingle($domainInput);
        }

        return $results;
    }

    private function lookupSingle(string $domainInput): BulkWhoisItemResult
    {
        try {
            $domain = $this->domainNameParser->parse($domainInput);
            $record = $this->repository->lookup($domain);

            return new BulkWhoisItemResult(
                domain: $domain->toString(),
                success: true,
                record: $record,
                message: null,
            );
        } catch (InvalidDomainException|WhoisLookupException $exception) {
            return new BulkWhoisItemResult(
                domain: $domainInput,
                success: false,
                record: null,
                message: $exception->getMessage(),
            );
        }
    }
}
