import { defineStore } from 'pinia'
import { ref } from 'vue'
import { productService } from '../services/productService'
import type { Product, ProductFilterParams, ProductFormData, ProductStats } from '../types'

export const useProductStore = defineStore('product', () => {
  const products = ref<Product[]>([])
  const stats = ref<ProductStats | null>(null)
  const isLoading = ref<boolean>(false)
  const isActionLoading = ref<boolean>(false)

  async function fetchProducts(params?: ProductFilterParams) {
    isLoading.value = true
    try {
      const response = await productService.getProducts(params)
      products.value = response.data
      return response
    } finally {
      isLoading.value = false
    }
  }

  async function fetchStats() {
    try {
      const data = await productService.getStats()
      stats.value = data
      return data
    } catch (e) {
      console.error(e)
    }
  }

  async function createProduct(payload: ProductFormData) {
    isActionLoading.value = true
    try {
      return await productService.createProduct(payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function updateProduct(id: number, payload: ProductFormData) {
    isActionLoading.value = true
    try {
      return await productService.updateProduct(id, payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function deleteProduct(id: number) {
    isActionLoading.value = true
    try {
      return await productService.deleteProduct(id)
    } finally {
      isActionLoading.value = false
    }
  }

  async function toggleStatus(product: Product) {
    const result = await productService.toggleStatus(product.id)
    product.is_active = result.is_active
    return result
  }

  async function reorderProducts(items: { id: number; sort_order: number }[]) {
    await productService.reorderProducts(items)
  }

  return {
    products,
    stats,
    isLoading,
    isActionLoading,
    fetchProducts,
    fetchStats,
    createProduct,
    updateProduct,
    deleteProduct,
    toggleStatus,
    reorderProducts,
  }
})
