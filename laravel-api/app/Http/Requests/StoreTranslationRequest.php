<?php

namespace App\Http\Requests;

use App\Enums\Locale;
use App\Enums\Tag;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTranslationRequest extends FormRequest
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
            'key' => ['required', 'string', 'max:255'],
            'locale' => ['required', 'string', Rule::in(Locale::values())],
            'content' => ['required', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', Rule::in(Tag::values())],
        ];
    }

    public function messages(): array
    {
        return [
            'locale.in' => sprintf(
                'The supported locales are: %s.',
                implode(', ', Locale::values())
            ),
            'tags.*.in' => sprintf(
                'Each tag must be one of: %s.',
                implode(', ', Tag::values())
            ),
        ];
    }
}
