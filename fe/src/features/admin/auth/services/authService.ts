import { apiClient } from '@/services/apiClient'
import type { ApiResponse, LoginFormData, LoginResponse } from '../types'

export const authService = {
  async login(payload: LoginFormData): Promise<LoginResponse> {
    const response = await apiClient.post<ApiResponse<LoginResponse>>('/admin/auth/login', payload)
    return response.data.data
  },
  async logout(): Promise<void> {
    await apiClient.post('/admin/auth/logout')
  },
}
