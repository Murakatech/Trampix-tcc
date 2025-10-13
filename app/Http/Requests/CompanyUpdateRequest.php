<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompanyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Regras para uso direto no Validator
     */
    public static function rulesFor(?int $companyId = null): array
    {
        return [
            'name' => 'required|string|max:255',
            'cnpj' => [
                'nullable',
                'string',
                'max:18',
                Rule::unique('companies', 'cnpj')->ignore($companyId)
            ],
            'sector' => 'nullable|string|max:100',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:1000',
            'website' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'employees_count' => 'nullable|integer|min:1|max:999999',
            'founded_year' => 'nullable|integer|min:1800|max:' . date('Y'),
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean',
        ];
    }

    public function rules(): array
    {
        $companyId = $this->user()?->company?->id;
        return self::rulesFor($companyId);
    }
}