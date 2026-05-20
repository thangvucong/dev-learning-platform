<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\User;

class StoreCourseRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'alpha_dash', 'unique:courses,slug'],
            'instructor_id' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    $exists = User::query()
                        ->role(User::ROLE_INSTRUCTOR)
                        ->whereKey((int) $value)
                        ->exists();

                    if (!$exists) {
                        $fail('Giảng viên được chọn không hợp lệ.');
                    }
                },
            ],
            'description' => ['required', 'string', 'min:50'],
            'thumbnail_url' => ['nullable', 'url', 'max:2048'],
            'intro_video_url' => ['nullable', 'url', 'max:2048'],
            'status' => ['required', Rule::in(['0', '1', 0, 1])],
            'published_at' => ['nullable', 'date'],
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
