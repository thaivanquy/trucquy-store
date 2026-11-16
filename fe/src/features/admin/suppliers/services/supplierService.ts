import { apiClient } from '@/services/apiClient'
import type { Bank, Province, Supplier, SupplierFilterParams, SupplierFormData, Ward } from '../types'

export const supplierService = {
  async getSuppliers(params: SupplierFilterParams = {}): Promise<{
    data: Supplier[]
    total: number
    current_page: number
    last_page: number
    per_page?: number
  }> {
    const response = await apiClient.get('/admin/suppliers', { params })
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

  async createSupplier(formData: SupplierFormData): Promise<Supplier> {
    const response = await apiClient.post('/admin/suppliers', formData)
    return response.data?.data
  },

  async updateSupplier(id: number, formData: SupplierFormData): Promise<Supplier> {
    const response = await apiClient.put(`/admin/suppliers/${id}`, formData)
    return response.data?.data
  },

  async toggleStatus(id: number): Promise<Supplier> {
    const response = await apiClient.patch(`/admin/suppliers/${id}/toggle-status`)
    return response.data?.data
  },

  async updateSupplierStatus(id: number): Promise<Supplier> {
    return this.toggleStatus(id)
  },

  async deleteSupplier(id: number): Promise<void> {
    await apiClient.delete(`/admin/suppliers/${id}`)
  },

  async getProvinces(): Promise<Province[]> {
    const response = await apiClient.get('/admin/provinces')
    return response.data?.data || []
  },

  async getWards(provinceCode: number): Promise<Ward[]> {
    const response = await apiClient.get(`/admin/provinces/${provinceCode}/wards`)
    return response.data?.data || []
  },

  async getBanks(): Promise<Bank[]> {
    const response = await apiClient.get('/admin/banks')
    return response.data?.data || []
  },
}
