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
        return [
            'nis' => [
                'required', 'string', 'max:20',
                // Pastikan NIS unik di dalam sekolah ini saja
                Rule::unique('students')->where(function ($query) {
                    return $query->where('school_id', Auth::user()->school_id);
                })
            ],
            'name' => ['required', 'string', 'max:255'],
            'classroom_id' => ['required', 'exists:classrooms,id'],
            'gender' => ['required', 'in:L,P'],
            'parent_phone' => ['required', 'string', 'max:20'],
        ];
    }
}