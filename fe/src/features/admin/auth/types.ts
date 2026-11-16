import { z } from 'zod'

export const loginSchema = z.object({
  email: z
    .string({ required_error: 'Vui lòng nhập email hoặc tài khoản' })
    .min(1, 'Vui lòng nhập email hoặc tài khoản')
    .email('Email không đúng định dạng'),
  password: z
    .string({ required_error: 'Vui lòng nhập mật khẩu' })
    .min(6, 'Mật khẩu phải từ 6 ký tự trở lên'),
  remember: z.boolean().default(false),
})

export type LoginFormData = z.infer<typeof loginSchema>

export interface LoginResponse {
  token: string
  user: {
    id: number
    name: string
    email: string
  }
}

