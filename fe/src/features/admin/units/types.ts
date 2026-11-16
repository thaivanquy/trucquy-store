import { z } from 'zod'

export const unitSchema = z.object({
  id: z.number().optional(),
  name: z
    .string({ required_error: 'Vui lòng nhập tên đơn vị tính' })
    .trim()
    .min(1, 'Vui lòng nhập tên đơn vị tính')
    .max(100, 'Tên đơn vị tính không được vượt quá 100 ký tự'),
  code: z
    .string()
    .trim()
    .max(50, 'Mã đơn vị tính không được vượt quá 50 ký tự')
    .optional()
    .or(z.literal('')),
  description: z.string().trim().nullable().optional().or(z.literal('')),
  sort_order: z.coerce.number().min(0, 'Thứ tự hiển thị phải lớn hơn hoặc bằng 0').default(1),
  is_active: z.boolean().default(true),
})

export interface Unit {
  id: number
  code: string
  name: string
  description: string | null
  sort_order: number
  is_active: boolean
  created_at?: string
  updated_at?: string
  deleted_at?: string | null
}

export type UnitFormData = {
  id?: number
  code?: string
  name: string
  description?: string | null
  sort_order?: number
  is_active?: boolean
}

export interface UnitFilterParams {
  search?: string
  is_active?: boolean | string
  page?: number
  per_page?: number
  all?: boolean
  sort_by?: string
  sort_direction?: 'asc' | 'desc'
}

export interface UnitStats {
  total: number
  active_count: number
  inactive_count: number
}
