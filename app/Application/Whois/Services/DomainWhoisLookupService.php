<?php

namespace App\Application\Whois\Services;

use App\Application\Whois\DTOs\BulkWhoisItemResult;
use App\Domain\Whois\Exceptions\InvalidDomainException;
use App\Domain\Whois\Exceptions\UserFacingException;
use App\Domain\Whois\Exceptions\WhoisLookupException;
use App\Domain\Whois\Exceptions\WhoisParseException;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\Services\DomainNameParser;
use Illuminate\Support\Facades\Log;
use Throwable;

class DomainWhoisLookupService
{
    public function __construct(
        private readonly WhoisRepositoryInterface $repository,
        private readonly DomainNameParser $domainNameParser,
    ) {}

    public function lookup(string $domainInput): BulkWhoisItemResult
    {
        try {
            $domain = $this->domainNameParser->parse($domainInput);
            $record = $this->repository->lookup($domain);

            return new BulkWhoisItemResult(
                domain: $domain->toString(),
                success: true,
                record: $record,
            );
        } catch (InvalidDomainException|WhoisLookupException|WhoisParseException $exception) {
            return $this->failureResult($domainInput, $exception);
        } catch (Throwable $exception) {
            Log::error($exception->getMessage(), [
                'domain' => $domainInput,
                'exception' => $exception,
            ]);

            return new BulkWhoisItemResult(
                domain: $domainInput,
                success: false,
                record: null,
                errorCode: 'server_error',
                message: 'Something went wrong on our end. Please try again in a moment.',
            );
        }
    }

    private function failureResult(string $domainInput, UserFacingException $exception): BulkWhoisItemResult
    {
        return new BulkWhoisItemResult(
            domain: $domainInput,
            success: false,
            record: null,
            errorCode: $exception->errorCode(),
            message: $exception->userMessage(),
        );
    }
}
