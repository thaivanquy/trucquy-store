import { defineStore } from 'pinia'
import { ref } from 'vue'
import { categoryService } from '../services/categoryService'
import type { Category, CategoryFilterParams, CategoryFormData, CategoryStats } from '../types'

export const useCategoryStore = defineStore('category', () => {
  const categories = ref<Category[]>([])
  const allCategories = ref<Category[]>([])
  const rootCategories = ref<Category[]>([])
  const stats = ref<CategoryStats | null>(null)
  const isLoading = ref<boolean>(false)
  const isActionLoading = ref<boolean>(false)

  async function fetchCategories(params?: CategoryFilterParams) {
    isLoading.value = true
    try {
      const response = await categoryService.getCategories(params)
      categories.value = response.data
      return response
    } finally {
      isLoading.value = false
    }
  }

  async function fetchDropdownCategories() {
    const res = await categoryService.getCategories({ all: true })
    allCategories.value = res.data
    rootCategories.value = res.data.filter((c) => !c.parent_id)
    return res.data
  }

  async function fetchStats() {
    try {
      const data = await categoryService.getStats()
      stats.value = data
      return data
    } catch (e) {
      console.error(e)
    }
  }

  async function createCategory(payload: CategoryFormData) {
    isActionLoading.value = true
    try {
      return await categoryService.createCategory(payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function updateCategory(id: number, payload: CategoryFormData) {
    isActionLoading.value = true
    try {
      return await categoryService.updateCategory(id, payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function deleteCategory(id: number) {
    isActionLoading.value = true
    try {
      return await categoryService.deleteCategory(id)
    } finally {
      isActionLoading.value = false
    }
  }

  async function toggleStatus(category: Category) {
    const result = await categoryService.toggleStatus(category.id)
    category.is_active = result.is_active
    return result
  }

  async function reorderCategories(items: { id: number; sort_order: number }[]) {
    await categoryService.reorderCategories(items)
  }

  return {
    categories,
    allCategories,
    rootCategories,
    stats,
    isLoading,
    isActionLoading,
    fetchCategories,
    fetchDropdownCategories,
    fetchStats,
    createCategory,
    updateCategory,
    deleteCategory,
    toggleStatus,
    reorderCategories,
  }
})
