<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
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
        return [
            'email' => 'required|email:rfc,dns|unique:users,email|max:255',
            'password' => 'required|min:3|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
        ];
    }
    public function messages(): array
    {
        return [
            'email.required' => 'Поле email обязательно для заполнения',
            'email.email' => 'Введите корректный email адрес',
            'email.unique' => 'Этот email уже зарегистрирован',
            'email.max' => 'Email не должен превышать 255 символов',

            'password.required' => 'Поле пароля обязательно для заполнения',
            'password.min' => 'Пароль должен содержать минимум 3 символа',
            'password.confirmed' => 'Пароли не совпадают',
            'password.regex' => 'Пароль должен содержать хотя бы одну заглавную букву, одну строчную букву и одну цифру',
        ];
    }
}
