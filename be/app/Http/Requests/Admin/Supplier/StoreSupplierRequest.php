<?php

namespace App\Http\Requests\Admin\Supplier;

use Illuminate\Foundation\Http\FormRequest;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['nullable', 'string', 'max:50', 'unique:suppliers,code'],
            'name' => ['required', 'string', 'max:200'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'string', 'email', 'max:150'],
            'address' => ['nullable', 'string', 'max:255'],
            'province_id' => ['nullable', 'integer'],
            'ward_id' => ['nullable', 'integer'],
            'contact_person' => ['nullable', 'string', 'max:150'],
            'contact_phone' => ['nullable', 'string', 'max:20'],
            'bank_name' => ['nullable', 'string', 'max:150'],
            'bank_account' => ['nullable', 'string', 'max:50'],
            'bank_account_name' => ['nullable', 'string', 'max:150'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'note' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên nhà cung cấp.',
            'name.max' => 'Tên nhà cung cấp không được vượt quá 200 ký tự.',
            'code.max' => 'Mã nhà cung cấp không được vượt quá 50 ký tự.',
            'code.unique' => 'Mã nhà cung cấp này đã tồn tại.',
            'email.email' => 'Địa chỉ email không đúng định dạng.',
            'email.max' => 'Địa chỉ email không được vượt quá 150 ký tự.',
            'phone.max' => 'Số điện thoại không được vượt quá 20 ký tự.',
            'tax_code.max' => 'Mã số thuế không được vượt quá 30 ký tự.',
            'address.max' => 'Địa chỉ không được vượt quá 255 ký tự.',
            'contact_person.max' => 'Tên người liên hệ không được vượt quá 150 ký tự.',
            'contact_phone.max' => 'SĐT người liên hệ không được vượt quá 20 ký tự.',
            'bank_name.max' => 'Tên ngân hàng không được vượt quá 150 ký tự.',
            'bank_account.max' => 'Số tài khoản không được vượt quá 50 ký tự.',
            'bank_account_name.max' => 'Tên chủ tài khoản không được vượt quá 150 ký tự.',
            'opening_balance.numeric' => 'Công nợ đầu kỳ phải là một số.',
            'opening_balance.min' => 'Công nợ đầu kỳ không được nhỏ hơn 0.',
        ];
    }
}
