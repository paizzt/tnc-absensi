<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class StoreStudentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Ambil school_id dari input hidden, atau fallback ke milik Auth (jika bukan Super Admin)
        $schoolId = $this->input('school_id') ?? Auth::user()->school_id;

        return [
            'school_id' => ['nullable', 'string'],
            'nis' => [
                'required', 'string', 'max:20',
                Rule::unique('students')->where(function ($query) use ($schoolId) {
                    return $query->where('school_id', $schoolId);
                })
            ],
            'name' => ['required', 'string', 'max:255'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'gender' => ['required', 'in:L,P'],
            'parent_phone' => ['required', 'string', 'max:20'],
        ];
    }
}