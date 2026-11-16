<?php

namespace App\Http\Requests\Admin\Unit;

use Illuminate\Foundation\Http\FormRequest;

class StoreUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => ['nullable', 'string', 'max:50', 'unique:units,code'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên đơn vị tính.',
            'name.max' => 'Tên đơn vị tính không được vượt quá 100 ký tự.',
            'code.max' => 'Mã đơn vị tính không được vượt quá 50 ký tự.',
            'code.unique' => 'Mã đơn vị tính này đã tồn tại.',
            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị phải lớn hơn hoặc bằng 0.',
        ];
    }
}
