<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Regras para uso direto no Validator (sem injeção do FormRequest)
     */
    public static function rulesFor(int $userId): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
        ];
    }

    public function rules(): array
    {
        $userId = $this->user()?->id ?? 0;

        return self::rulesFor($userId);
    }
}
