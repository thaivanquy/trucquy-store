import { z } from 'zod'

export const loginSchema = z.object({
  email: z
    .string({ required_error: 'Vui lòng nhập tài khoản hoặc email' })
    .trim()
    .min(1, 'Vui lòng nhập tài khoản hoặc email'),
  password: z
    .string({ required_error: 'Vui lòng nhập mật khẩu' })
    .min(6, 'Mật khẩu phải từ 6 ký tự trở lên'),
  remember: z.boolean().default(false),
})

export type LoginFormData = z.infer<typeof loginSchema>

export interface ApiResponse<T> {
  status: 'success' | 'error'
  code: number
  message?: string
  data: T
}

export interface LoginResponse {
  token: string
  token_type?: string
  expires_in?: number
  user: {
    id: number
    name: string
    email: string
  }
}
