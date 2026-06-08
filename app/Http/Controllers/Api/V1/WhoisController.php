<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\WhoisResource;
use App\Services\Whois\WhoisService;
use Illuminate\Http\JsonResponse;

class WhoisController extends Controller
{
    public function __construct(
        private readonly WhoisService $whoisService,
    ) {}

    public function show(string $domain): WhoisResource
    {
        return new WhoisResource($this->whoisService->lookup($domain));
    }

    public function raw(string $domain): JsonResponse
    {
        return response()->json([
            'domain' => $domain,
            'raw' => $this->whoisService->raw($domain),
        ]);
    }
}
