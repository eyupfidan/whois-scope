<?php

namespace App\Http\Requests\Api\V1;

use App\Domain\Whois\ValueObjects\LookupFormat;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LookupWhoisRequest extends FormRequest
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
            'format' => ['sometimes', 'string', Rule::enum(LookupFormat::class)],
        ];
    }

    public function lookupFormat(): LookupFormat
    {
        $format = $this->query('format', LookupFormat::Summary->value);

        return LookupFormat::fromString((string) $format);
    }
}
