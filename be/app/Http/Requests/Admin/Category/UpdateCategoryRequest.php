<?php

namespace App\Http\Requests\Admin\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('category') ?? $this->route('id');

        return [
            'parent_id' => [
                'nullable',
                'integer',
                Rule::exists('categories', 'id'),
                Rule::notIn([$id]),
            ],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('categories', 'code')->ignore($id)],
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', Rule::unique('categories', 'slug')->ignore($id)],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên danh mục.',
            'name.max' => 'Tên danh mục không được vượt quá 150 ký tự.',
            'code.max' => 'Mã danh mục không được vượt quá 50 ký tự.',
            'code.unique' => 'Mã danh mục này đã tồn tại.',
            'slug.max' => 'Đường dẫn slug không được vượt quá 180 ký tự.',
            'slug.unique' => 'Đường dẫn tĩnh (slug) này đã tồn tại.',
            'parent_id.exists' => 'Danh mục cha đã chọn không tồn tại.',
            'parent_id.not_in' => 'Danh mục cha không thể là chính danh mục này.',
            'image.max' => 'Đường dẫn hình ảnh không được vượt quá 255 ký tự.',
            'sort_order.integer' => 'Thứ tự sắp xếp phải là số nguyên.',
        ];
    }
}
