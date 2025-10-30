<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FreelancerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Regras para uso direto no Validator
     */
    public static function rulesFor(?int $freelancerId = null): array
    {
        return [
            'bio' => 'nullable|string|max:1000',
            'portfolio_url' => 'nullable|url|max:255',
            'phone' => 'nullable|string|max:20',
            'location' => 'nullable|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:999999.99',
            'availability' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'remove_cv' => 'nullable|boolean',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'service_categories' => 'nullable|array|max:10',
            'service_categories.*' => 'exists:service_categories,id',
        ];
    }

    public function rules(): array
    {
        $freelancerId = $this->user()?->freelancer?->id;
        return self::rulesFor($freelancerId);
    }
}