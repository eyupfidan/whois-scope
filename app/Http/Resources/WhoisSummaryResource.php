<?php

namespace App\Http\Resources;

use App\Domain\Whois\Entities\WhoisRecord;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin WhoisRecord */
class WhoisSummaryResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return $this->resource->toSummary();
    }
}
