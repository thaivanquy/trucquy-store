import { apiClient } from '@/services/apiClient'
import type { Unit, UnitFilterParams, UnitFormData, UnitStats } from '../types'

export const unitService = {
  async getUnits(params: UnitFilterParams = {}): Promise<{
    data: Unit[]
    total: number
    current_page: number
    last_page: number
    per_page?: number
  }> {
    const response = await apiClient.get('/admin/units', { params })
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

  async getStats(): Promise<UnitStats> {
    const response = await apiClient.get('/admin/units/stats')
    return response.data?.data
  },

  async createUnit(formData: UnitFormData): Promise<Unit> {
    const response = await apiClient.post('/admin/units', formData)
    return response.data?.data
  },

  async updateUnit(id: number, formData: UnitFormData): Promise<Unit> {
    const response = await apiClient.put(`/admin/units/${id}`, formData)
    return response.data?.data
  },

  async toggleStatus(id: number): Promise<Unit> {
    const response = await apiClient.patch(`/admin/units/${id}/toggle-status`)
    return response.data?.data
  },

  async deleteUnit(id: number): Promise<void> {
    await apiClient.delete(`/admin/units/${id}`)
  },

  async reorderUnits(items: { id: number; sort_order: number }[]): Promise<void> {
    await apiClient.patch('/admin/units/reorder', { items })
  },
}
