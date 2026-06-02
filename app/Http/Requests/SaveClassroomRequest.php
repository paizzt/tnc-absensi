<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveClassroomRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'level' => ['required', 'string', 'max:10'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
    
    public function messages(): array
    {
        return [
            'level.required' => 'Tingkat kelas wajib diisi (Contoh: X, XI, XII).',
            'name.required' => 'Nama kelas wajib diisi (Contoh: X MIPA 1).',
        ];
    }
}