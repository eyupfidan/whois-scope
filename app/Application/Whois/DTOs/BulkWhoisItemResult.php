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
}
