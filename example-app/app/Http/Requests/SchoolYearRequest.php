<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SchoolYearRequest extends FormRequest
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
            'school_year1' => ['required'],
            'school_year2' => ['required'],
            'enrollment_start' => ['required', 'date'],
            'enrollment_end' => ['required', 'date'],
            'school_start' => ['required', 'date'],
            'school_end' => ['required', 'date'],
        ];
    }
}
