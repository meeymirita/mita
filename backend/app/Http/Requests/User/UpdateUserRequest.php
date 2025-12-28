<?php

namespace App\Http\Requests\User;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = auth()->id();
        return [
            'name' => 'nullable|string|max:255|min:3|regex:/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u',
            'login' => ['nullable', 'string', 'max:255', 'min:3', 'alpha_dash',
                Rule::unique('users', 'login')->ignore($userId),
            ],
            'email' => ['required', 'email:rfc,dns', 'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
        ];
    }
}
