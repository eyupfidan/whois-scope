<?php

namespace App\Http\Controllers\Api\V1;

use App\Application\Whois\DTOs\BulkWhoisItemResult;
use App\Application\Whois\UseCases\BulkLookupWhoisUseCase;
use App\Application\Whois\UseCases\LookupWhoisUseCase;
use App\Domain\Whois\Entities\WhoisRecord;
use App\Domain\Whois\ValueObjects\LookupFormat;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\BulkLookupWhoisRequest;
use App\Http\Requests\Api\V1\LookupWhoisRequest;
use App\Http\Resources\WhoisFullResource;
use App\Http\Resources\WhoisSummaryResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class WhoisController extends Controller
{
    public function __construct(
        private readonly LookupWhoisUseCase $lookupWhoisUseCase,
        private readonly BulkLookupWhoisUseCase $bulkLookupWhoisUseCase,
    ) {}

    public function single(LookupWhoisRequest $request, string $domain): JsonResource
    {
        $record = $this->lookupWhoisUseCase->execute($domain);

        return $this->resourceFor($record, $request->lookupFormat());
    }

    public function bulk(BulkLookupWhoisRequest $request): JsonResponse
    {
        $format = $request->lookupFormat();
        $results = $this->bulkLookupWhoisUseCase->execute($request->domains());

        return response()->json([
            'format' => $format->value,
            'results' => array_map(
                fn (BulkWhoisItemResult $item) => $this->formatBulkItem($item, $format),
                $results,
            ),
        ]);
    }

    private function resourceFor(WhoisRecord $record, LookupFormat $format): JsonResource
    {
        return match ($format) {
            LookupFormat::Full => new WhoisFullResource($record),
            LookupFormat::Summary => new WhoisSummaryResource($record),
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function formatBulkItem(BulkWhoisItemResult $item, LookupFormat $format): array
    {
        if (! $item->success) {
            return [
                'domain' => $item->domain,
                'status' => 'error',
                'code' => $item->errorCode,
                'message' => $item->message,
            ];
        }

        return [
            'domain' => $item->domain,
            'status' => $item->record->registrationStatus->value,
            'data' => match ($format) {
                LookupFormat::Full => $item->record->toFull(),
                LookupFormat::Summary => $item->record->toSummary(),
            },
        ];
    }
}
