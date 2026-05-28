<?php

namespace App\Http\Requests\Teacher;

use App\Models\ClassSession;
use App\Models\CourseClass;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreLearningMaterialRequest extends FormRequest
{
    public function authorize()
    {
        return $this->user() !== null;
    }

    public function rules()
    {
        return [
            'class_id' => ['required', 'integer', 'exists:classes,id'],
            'class_session_id' => ['nullable', 'integer', 'exists:class_sessions,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'file' => ['required', 'file', 'max:51200', 'mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,zip,png,jpg,jpeg'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function (Validator $validator) {
            if (!$this->filled('class_id')) {
                return;
            }

            $class = CourseClass::query()->find((int) $this->input('class_id'));
            if (!$class || (int) $class->instructor_id !== (int) optional($this->user())->id) {
                $validator->errors()->add('class_id', 'Bạn không có quyền tải tài liệu cho lớp này.');

                return;
            }

            $sessionId = (int) $this->input('class_session_id');
            if ($sessionId <= 0) {
                return;
            }

            $belongsToClass = ClassSession::query()
                ->where('id', $sessionId)
                ->where('class_id', (int) $this->input('class_id'))
                ->exists();

            if (!$belongsToClass) {
                $validator->errors()->add('class_session_id', 'Buổi học không thuộc lớp đã chọn.');
            }
        });
    }

    public function messages()
    {
        return [
            'class_id.required' => 'Vui lòng chọn lớp học.',
            'file.required' => 'Vui lòng chọn file tài liệu.',
            'file.max' => 'Tài liệu không được vượt quá 50MB.',
            'file.mimes' => 'Định dạng tài liệu chưa được hỗ trợ.',
        ];
    }
}
