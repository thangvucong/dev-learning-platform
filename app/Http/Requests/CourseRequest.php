<?php
namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:courses,slug,' . ($this->course ?? ''),
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
            'status' => 'required|integer|in:0,1',
           
        ];
    }
}
