<?php

namespace App\Http\Requests\Api\V1;

use App\Domain\Whois\ValueObjects\LookupFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkLookupWhoisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'domains' => ['required', 'array', 'min:1', 'max:'.config('whois.bulk_limit')],
            'domains.*' => ['required', 'string', 'max:255'],
            'format' => ['sometimes', 'string', Rule::enum(LookupFormat::class)],
        ];
    }

    /**
     * @return list<string>
     */
    public function domains(): array
    {
        return $this->input('domains', []);
    }

    public function lookupFormat(): LookupFormat
    {
        $format = $this->input('format', LookupFormat::Summary->value);

        return LookupFormat::fromString((string) $format);
    }
}
