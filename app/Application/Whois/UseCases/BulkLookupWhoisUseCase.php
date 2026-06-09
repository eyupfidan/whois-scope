<?php

namespace App\Application\Whois\UseCases;

use App\Application\Whois\DTOs\BulkWhoisItemResult;
use App\Domain\Whois\Exceptions\InvalidDomainException;
use App\Domain\Whois\Exceptions\UserFacingException;
use App\Domain\Whois\Exceptions\WhoisLookupException;
use App\Domain\Whois\Exceptions\WhoisParseException;
use App\Domain\Whois\Repositories\WhoisRepositoryInterface;
use App\Domain\Whois\Services\DomainNameParser;
use Illuminate\Support\Facades\Log;
use Throwable;

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
        $this->ensureExecutionBudget(count($domainInputs));

        $results = [];

        foreach ($domainInputs as $domainInput) {
            $results[] = $this->lookupSingle($domainInput);
        }

        return $results;
    }

    private function ensureExecutionBudget(int $domainCount): void
    {
        if ($domainCount <= 0) {
            return;
        }

        $timeout = max(1, (int) config('whois.timeout', 10));
        $maxExecution = max(30, (int) config('whois.bulk_max_execution', 300));
        $budget = min($maxExecution, max(30, ($domainCount * $timeout * 3) + 15));

        set_time_limit($budget);
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
