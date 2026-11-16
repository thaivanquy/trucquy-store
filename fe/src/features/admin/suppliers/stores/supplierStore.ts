import { defineStore } from 'pinia'
import { ref } from 'vue'
import { supplierService } from '../services/supplierService'
import type { Bank, Province, Supplier, SupplierFilterParams, SupplierFormData, Ward } from '../types'

export const useSupplierStore = defineStore('supplier', () => {
  const suppliers = ref<Supplier[]>([])
  const provinces = ref<Province[]>([])
  const banks = ref<Bank[]>([])
  const wardsCache = ref<Record<number, Ward[]>>({})
  const isLoading = ref<boolean>(false)
  const isActionLoading = ref<boolean>(false)

  async function fetchSuppliers(params?: SupplierFilterParams) {
    isLoading.value = true
    try {
      const response = await supplierService.getSuppliers(params)
      suppliers.value = response.data
      return response
    } finally {
      isLoading.value = false
    }
  }

  async function fetchProvinces() {
    if (provinces.value.length > 0) return provinces.value
    const data = await supplierService.getProvinces()
    provinces.value = data
    return data
  }

  async function fetchBanks() {
    if (banks.value.length > 0) return banks.value
    const data = await supplierService.getBanks()
    banks.value = data
    return data
  }

  async function fetchWards(provinceCode: number) {
    if (wardsCache.value[provinceCode]) {
      return wardsCache.value[provinceCode]
    }
    const data = await supplierService.getWards(provinceCode)
    wardsCache.value[provinceCode] = data
    return data
  }

  async function createSupplier(payload: SupplierFormData) {
    isActionLoading.value = true
    try {
      return await supplierService.createSupplier(payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function updateSupplier(id: number, payload: SupplierFormData) {
    isActionLoading.value = true
    try {
      return await supplierService.updateSupplier(id, payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function deleteSupplier(id: number) {
    isActionLoading.value = true
    try {
      return await supplierService.deleteSupplier(id)
    } finally {
      isActionLoading.value = false
    }
  }

  async function toggleStatus(supplier: Supplier) {
    const result = await supplierService.toggleStatus(supplier.id)
    supplier.is_active = result.is_active
    return result
  }

  return {
    suppliers,
    provinces,
    banks,
    wardsCache,
    isLoading,
    isActionLoading,
    fetchSuppliers,
    fetchProvinces,
    fetchBanks,
    fetchWards,
    createSupplier,
    updateSupplier,
    deleteSupplier,
    toggleStatus,
  }
})
