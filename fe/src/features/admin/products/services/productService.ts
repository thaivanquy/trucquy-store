import { apiClient } from '@/services/apiClient'
import type { Product, ProductFilterParams, ProductFormData, ProductStats } from '../types'

export const productService = {
  async getProducts(params: ProductFilterParams = {}): Promise<{
    data: Product[]
    total: number
    current_page: number
    last_page: number
    per_page?: number
  }> {
    const response = await apiClient.get('/admin/products', { params })
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

  async getStats(): Promise<ProductStats> {
    const response = await apiClient.get('/admin/products/stats')
    return response.data?.data
  },

  async createProduct(formData: ProductFormData): Promise<Product> {
    const response = await apiClient.post('/admin/products', formData)
    return response.data?.data
  },

  async updateProduct(id: number, formData: ProductFormData): Promise<Product> {
    const response = await apiClient.put(`/admin/products/${id}`, formData)
    return response.data?.data
  },

  async toggleStatus(id: number): Promise<Product> {
    const response = await apiClient.patch(`/admin/products/${id}/toggle-status`)
    return response.data?.data
  },

  async deleteProduct(id: number): Promise<void> {
    await apiClient.delete(`/admin/products/${id}`)
  },

  async reorderProducts(items: { id: number; sort_order: number }[]): Promise<void> {
    await apiClient.patch('/admin/products/reorder', { items })
  },
}
