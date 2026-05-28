<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class GradeAssignmentSubmissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'score' => ['nullable', 'numeric', 'min:0', 'max:10', 'required_without:feedback'],
            'feedback' => ['nullable', 'string', 'max:2000', 'required_without:score'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'score.required_without' => 'Vui lòng nhập điểm hoặc nhận xét.',
            'score.numeric' => 'Điểm phải là số.',
            'score.min' => 'Điểm không được nhỏ hơn :min.',
            'score.max' => 'Điểm không được lớn hơn :max.',
            'feedback.required_without' => 'Vui lòng nhập điểm hoặc nhận xét.',
            'feedback.max' => 'Nhận xét tối đa :max ký tự.',
        ];
    }
}
