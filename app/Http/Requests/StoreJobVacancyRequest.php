<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobVacancyRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Autorização continua sendo verificada no Controller via Gate.
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => 'required|string|max:255',
            'description'   => 'required|string',
            'requirements'  => 'nullable|string',
            'segment_id'    => ['required','exists:segments,id'],
            'category_id'   => ['nullable','exists:categories,id'],
            'location_type' => 'nullable|in:Remoto,Híbrido,Presencial',
            'salary_range'  => 'nullable|string|max:100',
            'company_id'    => ['nullable','exists:companies,id'],
        ];
    }
}