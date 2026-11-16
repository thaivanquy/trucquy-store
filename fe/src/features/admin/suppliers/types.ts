import { z } from 'zod'

export interface Province {
  code: number
  name: string
  division_type?: string
  codename?: string
  phone_code?: number
}

export interface Ward {
  code: number
  province_code: number
  name: string
  division_type?: string
  codename?: string
  short_codename?: string
}

export interface Bank {
  id: number
  name: string
  code?: string
  bin?: string
  short_name?: string
  logo?: string
  transfer_supported?: boolean
  lookup_supported?: boolean
}

export interface Supplier {
  id: number
  code: string
  name: string
  phone: string | null
  email: string | null
  address: string | null
  province_id: number | null
  ward_id: number | null
  contact_person: string | null
  contact_phone: string | null
  bank_name: string | null
  bank_account: string | null
  bank_account_name: string | null
  opening_balance: number
  note: string | null
  is_active: boolean
  province?: { code: number; name: string; division_type?: string }
  ward?: { code: number; name: string; division_type?: string }
  created_at?: string
  updated_at?: string
  deleted_at?: string | null
}

export const supplierSchema = z.object({
  id: z.number().optional(),
  code: z.string().trim().max(50, 'Mã nhà cung cấp không vượt quá 50 ký tự').nullable().optional().or(z.literal('')),
  name: z
    .string({ required_error: 'Vui lòng nhập tên nhà cung cấp' })
    .trim()
    .min(1, 'Vui lòng nhập tên nhà cung cấp')
    .max(200, 'Tên nhà cung cấp tối đa 200 ký tự'),
  phone: z
    .string()
    .trim()
    .max(20, 'Số điện thoại không vượt quá 20 ký tự')
    .nullable()
    .optional()
    .or(z.literal('')),
  email: z
    .string()
    .trim()
    .max(150, 'Email không vượt quá 150 ký tự')
    .refine((val) => !val || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val), {
      message: 'Định dạng email không hợp lệ',
    })
    .nullable()
    .optional()
    .or(z.literal('')),
  address: z.string().trim().max(255, 'Địa chỉ không vượt quá 255 ký tự').nullable().optional().or(z.literal('')),
  province_id: z.number().nullable().optional(),
  ward_id: z.number().nullable().optional(),
  contact_person: z.string().trim().max(150, 'Tên người liên hệ không vượt quá 150 ký tự').nullable().optional().or(z.literal('')),
  contact_phone: z.string().trim().max(20, 'SĐT người liên hệ không vượt quá 20 ký tự').nullable().optional().or(z.literal('')),
  bank_name: z.string().trim().max(150, 'Tên ngân hàng không vượt quá 150 ký tự').nullable().optional().or(z.literal('')),
  bank_account: z.string().trim().max(50, 'Số tài khoản không vượt quá 50 ký tự').nullable().optional().or(z.literal('')),
  bank_account_name: z.string().trim().max(150, 'Tên chủ tài khoản không vượt quá 150 ký tự').nullable().optional().or(z.literal('')),
  opening_balance: z.coerce.number().default(0),
  note: z.string().trim().nullable().optional().or(z.literal('')),
  is_active: z.boolean().default(true),
})

export type SupplierFormData = {
  id?: number
  code?: string
  name: string
  phone?: string
  email?: string
  address?: string
  province_id?: number | null
  ward_id?: number | null
  contact_person?: string
  contact_phone?: string
  bank_name?: string | null
  bank_account?: string
  bank_account_name?: string
  opening_balance?: number
  note?: string | null
  is_active: boolean
}

export interface SupplierFilterParams {
  search?: string
  is_active?: boolean | string
  page?: number
  per_page?: number
}

