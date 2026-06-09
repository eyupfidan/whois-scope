<?php

namespace App\Application\Whois\DTOs;

use App\Domain\Whois\Entities\WhoisRecord;

final readonly class BulkWhoisItemResult
{
    public function __construct(
        public string $domain,
        public bool $success,
        public ?WhoisRecord $record,
        public ?string $errorCode = null,
        public ?string $message = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toPayload(): array
    {
        return [
            'domain' => $this->domain,
            'success' => $this->success,
            'record' => $this->record?->toCacheArray(),
            'error_code' => $this->errorCode,
            'message' => $this->message,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public static function fromPayload(array $payload): self
    {
        $record = is_array($payload['record'] ?? null)
            ? WhoisRecord::fromCacheArray($payload['record'])
            : null;

        return new self(
            domain: (string) $payload['domain'],
            success: (bool) $payload['success'],
            record: $record,
            errorCode: $payload['error_code'] ?? null,
            message: $payload['message'] ?? null,
        );
    }
}
