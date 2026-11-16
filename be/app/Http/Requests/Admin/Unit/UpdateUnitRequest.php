<?php

namespace App\Http\Requests\Admin\Unit;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUnitRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $unitId = (int) $this->route('unit');

        return [
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('units', 'code')->ignore($unitId),
            ],
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
            'code.required' => 'Vui lòng nhập mã đơn vị tính.',
            'code.max' => 'Mã đơn vị tính không được vượt quá 50 ký tự.',
            'code.unique' => 'Mã đơn vị tính này đã tồn tại.',
            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị phải lớn hơn hoặc bằng 0.',
        ];
    }
}
