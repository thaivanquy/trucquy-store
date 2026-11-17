import { z } from 'zod'

export const productSchema = z.object({
  id: z.number().optional(),
  name: z
    .string({ required_error: 'Vui lòng nhập tên sản phẩm' })
    .trim()
    .min(1, 'Vui lòng nhập tên sản phẩm')
    .max(200, 'Tên sản phẩm không được vượt quá 200 ký tự'),
  code: z
    .string()
    .trim()
    .max(50, 'Mã sản phẩm không được vượt quá 50 ký tự')
    .optional()
    .or(z.literal('')),
  barcode: z
    .string()
    .trim()
    .max(50, 'Mã vạch không được vượt quá 50 ký tự')
    .nullable()
    .optional()
    .or(z.literal('')),
  category_id: z.coerce.number().nullable().optional(),
  unit_id: z.coerce.number().nullable().optional(),
  supplier_id: z.coerce.number().nullable().optional(),
  cost_price: z.coerce.number().min(0, 'Giá vốn nhập hàng không được nhỏ hơn 0').default(0),
  selling_price: z.coerce.number().min(0, 'Giá bán không được nhỏ hơn 0'),
  stock_quantity: z.coerce.number().min(0, 'Tồn kho không được nhỏ hơn 0').default(0),
  min_stock: z.coerce.number().min(0, 'Tồn kho tối thiểu không được nhỏ hơn 0').default(5),
  image: z.string().nullable().optional().or(z.literal('')),
  description: z.string().trim().nullable().optional().or(z.literal('')),
  is_active: z.boolean().default(true),
})

export interface ProductUnitItem {
  id?: number
  unit_id: number | null
  barcode?: string | null
  conversion_rate: number
  is_base_unit: boolean
  cost_price: number
  selling_price: number
  is_direct_sale?: boolean
  unit?: { id: number; name: string; code: string } | null
}

export interface ProductSupplierItem {
  id?: number
  supplier_id: number | null
  supplier_product_code?: string | null
  purchase_price: number
  is_default: boolean
  supplier?: { id: number; name: string; code: string } | null
}

export interface InventoryItem {
  id?: number
  quantity: number
  min_stock: number
  max_stock?: number | null
  cost_price: number
}

export interface Product {
  id: number
  code: string
  barcode?: string | null
  name: string
  slug: string
  category_id?: number | null
  unit_id?: number | null
  supplier_id?: number | null
  cost_price: number
  selling_price: number
  stock_quantity: number
  min_stock: number
  image?: string | null
  description?: string | null
  is_active: boolean
  created_at?: string
  updated_at?: string
  category?: { id: number; name: string; code: string } | null
  unit?: { id: number; name: string; code: string } | null
  supplier?: { id: number; name: string; code: string } | null
  product_units?: ProductUnitItem[]
  product_suppliers?: ProductSupplierItem[]
  inventory?: InventoryItem | null
}

export type ProductFormData = {
  id?: number
  code?: string
  barcode?: string | null
  name: string
  category_id?: number | null
  unit_id?: number | null
  supplier_id?: number | null
  cost_price?: number
  selling_price: number
  stock_quantity?: number
  min_stock?: number
  image?: string | null
  description?: string | null
  is_active?: boolean
  units?: ProductUnitItem[]
  suppliers?: ProductSupplierItem[]
}

export interface ProductFilterParams {
  search?: string
  category_id?: number | string | null
  unit_id?: number | string | null
  supplier_id?: number | string | null
  is_active?: boolean | string
  low_stock?: boolean | string
  out_of_stock?: boolean | string
  page?: number
  per_page?: number
  all?: boolean
  sort_by?: string
  sort_direction?: 'asc' | 'desc'
}

export interface ProductStats {
  total: number
  active_count: number
  inactive_count: number
  low_stock_count: number
  out_of_stock_count: number
}
