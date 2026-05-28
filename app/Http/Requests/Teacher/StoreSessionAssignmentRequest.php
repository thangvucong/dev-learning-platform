<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreSessionAssignmentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string', 'required_without:attachment'],
            'submission_type' => ['required', 'in:text,file,both'],
            'due_at' => ['nullable', 'date'],
            'status' => ['required', 'in:draft,published'],
            'attachment' => ['nullable', 'file', 'max:20480', 'required_without:content'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề bài tập.',
            'title.max' => 'Tiêu đề bài tập tối đa :max ký tự.',
            'content.required_without' => 'Vui lòng nhập nội dung hoặc đính kèm file bài tập.',
            'submission_type.required' => 'Vui lòng chọn kiểu nộp bài.',
            'submission_type.in' => 'Kiểu nộp bài không hợp lệ.',
            'due_at.date' => 'Hạn nộp không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái bài tập.',
            'status.in' => 'Trạng thái bài tập không hợp lệ.',
            'attachment.required_without' => 'Vui lòng nhập nội dung hoặc đính kèm file bài tập.',
            'attachment.file' => 'File đính kèm không hợp lệ.',
            'attachment.max' => 'File đính kèm tối đa :max KB.',
        ];
    }
}
