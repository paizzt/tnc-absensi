<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Karena sistem role sudah ada, pastikan hanya Super Admin yang bisa (akan di-handle di middleware nanti).
        // Untuk saat ini kita set true agar request bisa lolos ke proses validasi.
        return true; 
    }

    public function rules(): array
    {
        return [
            'npsn' => ['required', 'string', 'max:20', 'unique:schools,npsn'],
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
            'npsn.unique' => 'NPSN ini sudah terdaftar di sistem.',
            'name.required' => 'Nama Sekolah wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
        ];
    }
}