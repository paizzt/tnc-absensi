<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil ID sekolah dari parameter rute (URL)
        $schoolId = $this->route('school');

        return [
            // Pengecualian ID saat mengecek keunikan NPSN
            'npsn' => ['required', 'string', 'max:20', Rule::unique('schools')->ignore($schoolId)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'npsn.required' => 'NPSN wajib diisi.',
            'npsn.unique' => 'NPSN ini sudah digunakan oleh sekolah lain.',
            'name.required' => 'Nama Sekolah wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ];
    }
}