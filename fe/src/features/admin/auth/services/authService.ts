import { apiClient } from '@/services/apiClient'
import type { LoginFormData, LoginResponse } from '../types'

export const authService = {
  async login(payload: LoginFormData): Promise<LoginResponse> {
    const response = await apiClient.post<LoginResponse>('/auth/login', payload)
    return response.data
  },
  async logout(): Promise<void> {
    await apiClient.post('/auth/logout')
  },
}
