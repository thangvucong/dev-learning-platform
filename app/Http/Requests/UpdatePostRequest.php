<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    /**
     * @return bool
     */
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:10', 'max:255'],
            'content' => ['required', 'string', 'min:50'],
            'thumbnail' => ['nullable', 'image', 'max:2048'],
            'image' => ['nullable', 'image', 'max:4096'],
            'action' => ['required', 'in:draft,pending'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề.',
            'title.min' => 'Tiêu đề phải có ít nhất :min ký tự.',
            'title.max' => 'Tiêu đề tối đa :max ký tự.',
            'content.required' => 'Vui lòng nhập nội dung bài viết.',
            'content.min' => 'Nội dung phải có ít nhất :min ký tự.',
            'thumbnail.image' => 'Thumbnail phải là file ảnh hợp lệ.',
            'thumbnail.max' => 'Thumbnail tối đa :max KB.',
            'image.image' => 'Ảnh cover phải là file ảnh hợp lệ.',
            'image.max' => 'Ảnh cover tối đa :max KB.',
            'action.in' => 'Hành động không hợp lệ.',
        ];
    }
}

