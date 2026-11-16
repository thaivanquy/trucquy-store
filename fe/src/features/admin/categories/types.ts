import { z } from 'zod'

export const categorySchema = z.object({
  id: z.number().optional(),
  parent_id: z.number().nullable().optional(),
  name: z
    .string({ required_error: 'Vui lòng nhập tên danh mục' })
    .trim()
    .min(1, 'Vui lòng nhập tên danh mục')
    .max(150, 'Tên danh mục không vượt quá 150 ký tự'),
  code: z
    .string({ required_error: 'Vui lòng nhập mã danh mục' })
    .trim()
    .min(1, 'Vui lòng nhập mã danh mục')
    .max(50, 'Mã danh mục không vượt quá 50 ký tự'),
  slug: z
    .string({ required_error: 'Vui lòng nhập slug danh mục' })
    .trim()
    .min(1, 'Vui lòng nhập slug danh mục')
    .max(180, 'Slug không vượt quá 180 ký tự'),
  description: z.string().trim().nullable().optional().or(z.literal('')),
  image: z.string().nullable().optional().or(z.literal('')),
  sort_order: z.coerce.number().min(0, 'Thứ tự hiển thị phải lớn hơn hoặc bằng 0').default(1),
  is_active: z.boolean().default(true),
})

export interface Category {
  id: number
  parent_id: number | null
  code: string
  name: string
  slug: string
  description: string | null
  image: string | null
  sort_order: number
  is_active: boolean
  created_at?: string
  updated_at?: string
  deleted_at?: string | null
  parent?: Category | null
  children?: Category[]
}

export type CategoryFormData = {
  id?: number
  parent_id: number | null
  code: string
  name: string
  slug: string
  description?: string | null
  image?: string | null
  sort_order: number
  is_active: boolean
}

export interface CategoryFilterParams {
  search?: string
  parent_id?: number | string | null
  is_active?: boolean | string
  page?: number
  per_page?: number
  all?: boolean
  tree?: boolean
}

export interface CategoryStats {
  total: number
  active_count: number
  inactive_count: number
  root_count: number
  sub_count: number
}
