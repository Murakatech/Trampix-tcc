<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'display_name' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('freelancers', 'display_name')->ignore($freelancerId),
            ],
            'bio' => 'nullable|string|max:1000',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'whatsapp' => 'nullable|string',
            'location' => 'nullable|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0|max:999999.99',
            'availability' => 'nullable|string|max:255',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'remove_cv' => 'nullable|boolean',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'is_active' => 'nullable|boolean',
            'service_categories' => 'nullable|array|max:10',
            'service_categories.*' => 'exists:service_categories,id',
            // Novos campos: segmentos do freelancer
            'segments' => 'nullable|array|max:3',
            'segments.*' => 'exists:segments,id',
            // Removido: segmento principal único; usamos até 3 segmentos (acima)
        ];
    }

    public function rules(): array
    {
        $freelancerId = $this->user()?->freelancer?->id;

        return self::rulesFor($freelancerId);
    }
}
