import { apiClient } from '@/services/apiClient'
import type { Category, CategoryFilterParams, CategoryFormData, CategoryStats } from '../types'

export const categoryService = {
  async getCategories(params: CategoryFilterParams = {}): Promise<{
    data: Category[]
    total: number
    current_page: number
    last_page: number
    per_page?: number
  }> {
    const response = await apiClient.get('/admin/categories', { params })
    const result = response.data?.data

    if (Array.isArray(result)) {
      return {
        data: result,
        total: result.length,
        current_page: 1,
        last_page: 1,
        per_page: result.length,
      }
    }

    return {
      data: result?.data || [],
      total: result?.total ?? (result?.data ? result.data.length : 0),
      current_page: result?.current_page || 1,
      last_page: result?.last_page || 1,
      per_page: result?.per_page || 10,
    }
  },

  async getStats(): Promise<CategoryStats> {
    const response = await apiClient.get('/admin/categories/stats')
    return response.data?.data
  },

  async createCategory(formData: CategoryFormData): Promise<Category> {
    const response = await apiClient.post('/admin/categories', formData)
    return response.data?.data
  },

  async updateCategory(id: number, formData: CategoryFormData): Promise<Category> {
    const response = await apiClient.put(`/admin/categories/${id}`, formData)
    return response.data?.data
  },

  async toggleStatus(id: number): Promise<Category> {
    const response = await apiClient.patch(`/admin/categories/${id}/toggle-status`)
    return response.data?.data
  },

  async deleteCategory(id: number): Promise<void> {
    await apiClient.delete(`/admin/categories/${id}`)
  },

  async reorderCategories(items: { id: number; sort_order: number }[]): Promise<void> {
    await apiClient.patch('/admin/categories/reorder', { items })
  },
}
