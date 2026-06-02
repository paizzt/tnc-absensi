<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveSubjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'name.required' => 'Nama Mata Pelajaran wajib diisi.',
        ];
    }
}