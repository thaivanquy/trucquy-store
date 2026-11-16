import { defineStore } from 'pinia'
import { ref } from 'vue'
import { authService } from '../services/authService'
import type { LoginFormData } from '../types'

export const useAuthStore = defineStore('auth', () => {
  const token = ref<string | null>(localStorage.getItem('token') || sessionStorage.getItem('token'))
  const user = ref<{ id: number; name: string; email: string } | null>(null)
  const isLoading = ref<boolean>(false)
  const errorMessage = ref<string | null>(null)

  async function login(credentials: LoginFormData) {
    isLoading.value = true
    errorMessage.value = null
    try {
      const data = await authService.login(credentials)
      token.value = data.token
      user.value = data.user
      if (credentials.remember) {
        localStorage.setItem('token', data.token)
      } else {
        sessionStorage.setItem('token', data.token)
      }
      return data
    } catch (err: unknown) {
      const error = err as { response?: { data?: { message?: string } } }
      errorMessage.value = error.response?.data?.message || 'Đăng nhập không thành công. Vui lòng thử lại!'
      throw err
    } finally {
      isLoading.value = false
    }
  }

  async function logout() {
    try {
      await authService.logout()
    } catch {
    } finally {
      token.value = null
      user.value = null
      localStorage.removeItem('token')
      sessionStorage.removeItem('token')
    }
  }

  return {
    token,
    user,
    isLoading,
    errorMessage,
    login,
    logout,
  }
})
