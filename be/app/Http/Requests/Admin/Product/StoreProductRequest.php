<?php

namespace App\Http\Requests\Admin\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'code' => ['nullable', 'string', 'max:50', 'unique:products,code'],
            'barcode' => ['nullable', 'string', 'max:50'],
            'slug' => ['nullable', 'string', 'max:220', 'unique:products,slug'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'unit_id' => ['nullable', 'integer', 'exists:units,id'],
            'supplier_id' => ['nullable', 'integer', 'exists:suppliers,id'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'selling_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'numeric', 'min:0'],
            'min_stock' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],

            'units' => ['nullable', 'array'],
            'units.*.unit_id' => ['required_with:units', 'integer', 'exists:units,id'],
            'units.*.barcode' => ['nullable', 'string', 'max:50'],
            'units.*.conversion_rate' => ['nullable', 'numeric', 'min:0.0001'],
            'units.*.is_base_unit' => ['nullable', 'boolean'],
            'units.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'units.*.selling_price' => ['nullable', 'numeric', 'min:0'],

            'suppliers' => ['nullable', 'array'],
            'suppliers.*.supplier_id' => ['required_with:suppliers', 'integer', 'exists:suppliers,id'],
            'suppliers.*.supplier_product_code' => ['nullable', 'string', 'max:50'],
            'suppliers.*.purchase_price' => ['nullable', 'numeric', 'min:0'],
            'suppliers.*.is_default' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Vui lòng nhập tên sản phẩm.',
            'name.max' => 'Tên sản phẩm không được vượt quá 200 ký tự.',
            'code.max' => 'Mã sản phẩm không được vượt quá 50 ký tự.',
            'code.unique' => 'Mã sản phẩm này đã tồn tại.',
            'barcode.max' => 'Mã vạch không được vượt quá 50 ký tự.',
            'slug.unique' => 'Đường dẫn tĩnh (slug) này đã tồn tại.',
            'category_id.exists' => 'Danh mục đã chọn không tồn tại.',
            'unit_id.exists' => 'Đơn vị tính đã chọn không tồn tại.',
            'supplier_id.exists' => 'Nhà cung cấp đã chọn không tồn tại.',
            'cost_price.numeric' => 'Giá vốn nhập hàng phải là một số.',
            'cost_price.min' => 'Giá vốn nhập hàng không được nhỏ hơn 0.',
            'selling_price.numeric' => 'Giá bán phải là một số.',
            'selling_price.min' => 'Giá bán không được nhỏ hơn 0.',
            'stock_quantity.numeric' => 'Số lượng tồn kho phải là số.',
            'stock_quantity.min' => 'Số lượng tồn kho không được nhỏ hơn 0.',
            'min_stock.numeric' => 'Tồn kho tối thiểu phải là số.',
            'min_stock.min' => 'Tồn kho tối thiểu không được nhỏ hơn 0.',
        ];
    }
}
