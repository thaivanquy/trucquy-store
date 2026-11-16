import { defineStore } from 'pinia'
import { ref } from 'vue'
import { unitService } from '../services/unitService'
import type { Unit, UnitFilterParams, UnitFormData, UnitStats } from '../types'

export const useUnitStore = defineStore('unit', () => {
  const units = ref<Unit[]>([])
  const allUnits = ref<Unit[]>([])
  const stats = ref<UnitStats | null>(null)
  const isLoading = ref<boolean>(false)
  const isActionLoading = ref<boolean>(false)

  async function fetchUnits(params?: UnitFilterParams) {
    isLoading.value = true
    try {
      const response = await unitService.getUnits(params)
      units.value = response.data
      return response
    } finally {
      isLoading.value = false
    }
  }

  async function fetchAllUnits() {
    const res = await unitService.getUnits({ all: true })
    allUnits.value = res.data
    return res.data
  }

  async function fetchStats() {
    try {
      const data = await unitService.getStats()
      stats.value = data
      return data
    } catch (e) {
      console.error(e)
    }
  }

  async function createUnit(payload: UnitFormData) {
    isActionLoading.value = true
    try {
      return await unitService.createUnit(payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function updateUnit(id: number, payload: UnitFormData) {
    isActionLoading.value = true
    try {
      return await unitService.updateUnit(id, payload)
    } finally {
      isActionLoading.value = false
    }
  }

  async function deleteUnit(id: number) {
    isActionLoading.value = true
    try {
      return await unitService.deleteUnit(id)
    } finally {
      isActionLoading.value = false
    }
  }

  async function toggleStatus(unit: Unit) {
    const result = await unitService.toggleStatus(unit.id)
    unit.is_active = result.is_active
    return result
  }

  async function reorderUnits(items: { id: number; sort_order: number }[]) {
    await unitService.reorderUnits(items)
  }

  return {
    units,
    allUnits,
    stats,
    isLoading,
    isActionLoading,
    fetchUnits,
    fetchAllUnits,
    fetchStats,
    createUnit,
    updateUnit,
    deleteUnit,
    toggleStatus,
    reorderUnits,
  }
})
