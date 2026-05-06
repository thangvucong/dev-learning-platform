<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCourseClassRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'course_id' => ['required', 'integer', 'exists:courses,id'],
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:classes,code'],
            'status' => ['required', Rule::in(['upcoming', 'ongoing', 'completed'])],
      
            'mode' => ['required', Rule::in(['online', 'offline', 'zoom'])],
            'capacity' => ['required', 'integer', 'min:0', 'max:100000'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after_or_equal:start_at'],
            'location' => ['nullable', 'string', 'max:255'],
        ];
    }
}

