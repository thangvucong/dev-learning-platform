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
            'schedule_config' => ['nullable', 'array'],
            'schedule_config.generation_mode' => ['nullable', Rule::in(['auto', 'custom'])],
            'schedule_config.sessions_count' => ['nullable', 'integer', 'min:1', 'max:500'],
            'schedule_config.days_of_week' => ['required_if:schedule_config.generation_mode,custom', 'array', 'min:1'],
            'schedule_config.days_of_week.*' => ['integer', 'between:1,7'],
            'schedule_config.session_start_time' => ['nullable', 'date_format:H:i'],
            'schedule_config.session_end_time' => ['nullable', 'date_format:H:i'],
        ];
    }
}

