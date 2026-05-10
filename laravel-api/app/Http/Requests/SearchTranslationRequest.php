<?php

namespace App\Http\Requests;

use App\Enums\Locale;
use App\Enums\Tag;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SearchTranslationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'tag' => ['nullable', 'string', Rule::in(Tag::values())],
            'key' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'max:1000'],
            'locale' => ['nullable', 'string', Rule::in(Locale::values())],
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tag.in' => sprintf(
                'The tag must be one of: %s.',
                implode(', ', Tag::values())
            ),
            'locale.in' => sprintf(
                'The locale must be one of: %s.',
                implode(', ', Locale::values())
            ),
            'per_page.max' => 'The per_page value may not be greater than 100.',
        ];
    }
}
