<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'string', 'exists:roles,name'],
            'school_id' => ['nullable', 'exists:schools,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Alamat email ini sudah terdaftar di sistem.',
            'password.min' => 'Kata sandi minimal 8 karakter.',
            'role.required' => 'Role pengguna wajib dipilih.',
        ];
    }
}