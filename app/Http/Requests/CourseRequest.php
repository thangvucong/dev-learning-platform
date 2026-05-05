<?php
namespace App\Http\Requests\Admin;

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
            'instructor_id' => 'required|exists:users,id',
            'status' => 'required|integer|in:0,1',
           
        ];
    }
}