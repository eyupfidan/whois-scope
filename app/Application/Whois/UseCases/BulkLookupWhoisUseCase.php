<?php

namespace App\Application\Whois\UseCases;

use App\Application\Whois\DTOs\BulkWhoisItemResult;
use App\Application\Whois\Services\DomainWhoisLookupService;
use App\Application\Whois\Tasks\LookupWhoisDomainTask;
use App\Domain\Whois\Exceptions\InvalidDomainException;
use App\Domain\Whois\Services\DomainNameParser;
use App\Infrastructure\Whois\CachedWhoisRepository;
use Illuminate\Support\Facades\Concurrency;

class BulkLookupWhoisUseCase
{
    public function __construct(
        private readonly DomainWhoisLookupService $lookupService,
        private readonly DomainNameParser $domainNameParser,
        private readonly CachedWhoisRepository $cachedRepository,
    ) {}

    /**
     * @param  list<string>  $domainInputs
     * @return list<BulkWhoisItemResult>
     */
    public function execute(array $domainInputs): array
    {
        $this->ensureExecutionBudget(count($domainInputs));

        $results = array_fill(0, count($domainInputs), null);
        $pending = [];

        foreach ($domainInputs as $index => $domainInput) {
            try {
                $domain = $this->domainNameParser->parse($domainInput);
                $cached = $this->cachedRepository->findCached($domain);

                if ($cached !== null) {
                    $results[$index] = new BulkWhoisItemResult(
                        domain: $domain->toString(),
                        success: true,
                        record: $cached,
                    );

                    continue;
                }

                $pending[$index] = $domainInput;
            } catch (InvalidDomainException $exception) {
                $results[$index] = new BulkWhoisItemResult(
                    domain: $domainInput,
                    success: false,
                    record: null,
                    errorCode: $exception->errorCode(),
                    message: $exception->userMessage(),
                );
            }
        }

        if ($pending === []) {
            return array_values($results);
        }

        $lookups = $this->lookupPendingDomains($pending);

        foreach ($lookups as $index => $result) {
            $results[$index] = $result;
        }

        return array_values($results);
    }

    /**
     * @param  array<int, string>  $pending
     * @return array<int, BulkWhoisItemResult>
     */
    private function lookupPendingDomains(array $pending): array
    {
        $concurrency = max(1, (int) config('whois.bulk_concurrency', 5));

        if (count($pending) === 1 || $concurrency === 1) {
            $results = [];

            foreach ($pending as $index => $domainInput) {
                $results[$index] = $this->lookupService->lookup($domainInput);
            }

            return $results;
        }

        $results = [];

        foreach (array_chunk($pending, $concurrency, true) as $chunk) {
            $tasks = [];

            foreach ($chunk as $index => $domainInput) {
                $tasks[$index] = static fn (): array => LookupWhoisDomainTask::handle($domainInput);
            }

            try {
                foreach (Concurrency::run($tasks) as $index => $payload) {
                    $results[$index] = BulkWhoisItemResult::fromPayload($payload);
                }
            } catch (\Throwable) {
                foreach ($chunk as $index => $domainInput) {
                    $results[$index] = $this->lookupService->lookup($domainInput);
                }
            }
        }

        return $results;
    }

    private function ensureExecutionBudget(int $domainCount): void
    {
        if ($domainCount <= 0) {
            return;
        }

        $timeout = max(1, (int) config('whois.timeout', 8));
        $concurrency = max(1, (int) config('whois.bulk_concurrency', 5));
        $maxExecution = max(30, (int) config('whois.bulk_max_execution', 300));
        $batches = (int) ceil($domainCount / $concurrency);
        $budget = min($maxExecution, max(30, ($batches * $timeout * 3) + 15));

        set_time_limit($budget);
    }
}
