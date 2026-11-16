<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import {
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  PlusOutlined,
  TagOutlined,
  HolderOutlined,
} from '@ant-design/icons-vue'
import type { Unit, UnitFilterParams } from '../types'
import { useUnitStore } from '../stores/unitStore'
import UnitFormModal from '../components/UnitFormModal.vue'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'

const toast = useToast()
const { t } = useI18n()

const unitStore = useUnitStore()
const { units, isLoading } = storeToRefs(unitStore)

const filters = reactive<UnitFilterParams>({
  search: '',
  is_active: '',
})

const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0,
})

const isModalVisible = ref<boolean>(false)
const selectedUnit = ref<Unit | null>(null)

const draggedIndex = ref<number | null>(null)
const isReordering = ref<boolean>(false)

let debounceTimer: ReturnType<typeof setTimeout> | null = null

async function loadData() {
  try {
    const res = await unitStore.fetchUnits({
      search: filters.search ? filters.search.trim() : undefined,
      is_active: filters.is_active !== '' && filters.is_active !== undefined ? filters.is_active : undefined,
      page: pagination.current,
      per_page: pagination.pageSize,
    })
    pagination.total = res.total
    pagination.current = res.current_page
    pagination.pageSize = res.per_page || pagination.pageSize
  } catch (err: any) {
    toast.error(err.message || t('unit.loadError'))
  }
}

function handleSearchInput() {
  if (debounceTimer) {
    clearTimeout(debounceTimer)
  }
  debounceTimer = setTimeout(() => {
    pagination.current = 1
    loadData()
  }, 350)
}

function handleFilterChange() {
  pagination.current = 1
  loadData()
}

function handlePageChange(page: number, pageSize: number) {
  pagination.current = page
  pagination.pageSize = pageSize
  loadData()
}

function onDragStart(index: number, e: DragEvent) {
  draggedIndex.value = index
  if (e.dataTransfer) {
    e.dataTransfer.effectAllowed = 'move'
    e.dataTransfer.setData('text/plain', String(index))
  }
}

function onDragOver(index: number) {
  if (draggedIndex.value === null || draggedIndex.value === index) return
}

async function onDrop(targetIndex: number) {
  if (draggedIndex.value === null || draggedIndex.value === targetIndex) {
    draggedIndex.value = null
    return
  }

  const moved = units.value.splice(draggedIndex.value, 1)[0]
  if (moved) {
    units.value.splice(targetIndex, 0, moved)

    const items = units.value.map((u, idx) => ({
      id: u.id,
      sort_order: (pagination.current - 1) * pagination.pageSize + idx + 1,
    }))

    isReordering.value = true
    try {
      await unitStore.reorderUnits(items)
      toast.success(t('unit.reorderSuccess'))
    } catch (err: any) {
      toast.error(err.message || t('unit.reorderError'))
      loadData()
    } finally {
      isReordering.value = false
    }
  }
  draggedIndex.value = null
}

function onDragEnd() {
  draggedIndex.value = null
}

function openCreateModal() {
  selectedUnit.value = null
  isModalVisible.value = true
}

function openEditModal(record: Unit) {
  selectedUnit.value = record
  isModalVisible.value = true
}

async function handleToggleStatus(record: Unit) {
  try {
    await unitStore.toggleStatus(record)
    toast.success(t('unit.toggleStatusSuccess', { name: record.name }))
  } catch (err: any) {
    toast.error(err.message || t('unit.toggleStatusError'))
  }
}

async function handleDelete(record: Unit) {
  try {
    await unitStore.deleteUnit(record.id)
    toast.success(t('unit.deleteSuccess', { name: record.name }))
    loadData()
  } catch (err: any) {
    const apiErrors = err.response?.data?.errors
    const firstError = apiErrors ? Object.values(apiErrors).flat()[0] : null
    toast.error(firstError || err.response?.data?.message || err.message || t('unit.deleteError'))
  }
}

function handleModalSuccess() {
  loadData()
}

onMounted(() => {
  loadData()
})
</script>

<template>
  <div class="space-y-4 pb-12 sm:pb-16 overflow-x-hidden">
    <!-- Action Toolbar -->
    <div class="flex items-center justify-end gap-2.5 pb-0.5">
      <button
        type="button"
        @click="openCreateModal"
        class="inline-flex items-center justify-center gap-2 px-4 h-9 rounded-lg bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-sm font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer select-none"
      >
        <PlusOutlined class="text-xs text-white" />
        <span class="text-white leading-none">{{ t('unit.addButton') }}</span>
      </button>
    </div>

    <!-- Filters Toolbar -->
    <div class="bg-white rounded-xl border border-slate-200/90 p-3 sm:p-3.5 shadow-2xs flex flex-wrap items-center gap-3">
      <div class="w-full sm:w-80">
        <a-input
          v-model:value="filters.search"
          :placeholder="t('unit.searchPlaceholder')"
          allow-clear
          size="middle"
          class="!rounded-lg !text-sm !bg-slate-50/70 hover:!bg-white focus:!bg-white transition-colors"
          @input="handleSearchInput"
          @change="handleSearchInput"
        >
          <template #prefix><SearchOutlined class="text-slate-400 text-sm" /></template>
        </a-input>
      </div>

      <a-select
        v-model:value="filters.is_active"
        :placeholder="t('unit.filterStatus')"
        class="w-44 !rounded-lg !text-sm"
        allow-clear
        size="middle"
        @change="handleFilterChange"
      >
        <a-select-option value="">{{ t('unit.allStatuses') }}</a-select-option>
        <a-select-option value="true">{{ t('unit.statusActive') }}</a-select-option>
        <a-select-option value="false">{{ t('unit.statusInactive') }}</a-select-option>
      </a-select>
    </div>

    <!-- Loading State -->
    <div v-if="isLoading && units.length === 0" class="py-16 text-center bg-white rounded-xl border border-slate-200/90 shadow-2xs">
      <a-spin size="large" />
    </div>

    <!-- Empty State -->
    <div v-else-if="units.length === 0" class="py-14 text-center bg-white rounded-xl border border-slate-200/90 p-6 shadow-2xs">
      <a-empty />
    </div>

    <!-- Data Table -->
    <div v-else class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80">
              <th class="w-9 px-2 text-center text-xs font-semibold text-slate-400"></th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-32">{{ t('unit.colCode') }}</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider min-w-[160px]">{{ t('unit.colName') }}</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider min-w-[240px]">{{ t('unit.colDescription') }}</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap min-w-[90px]">{{ t('unit.colStatus') }}</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap min-w-[90px]">{{ t('unit.colActions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(unit, index) in units"
              :key="unit.id"
              draggable="true"
              @dragstart="onDragStart(index, $event)"
              @dragover.prevent="onDragOver(index)"
              @drop="onDrop(index)"
              @dragend="onDragEnd"
              class="hover:bg-slate-50/70 transition-all select-none"
              :class="{ 'opacity-40 ring-2 ring-sky-500 bg-sky-50/50 scale-[0.99]': draggedIndex === index }"
            >
              <!-- Drag Handle -->
              <td class="w-9 px-2 text-center text-slate-300 hover:text-slate-600 cursor-grab active:cursor-grabbing" :title="t('unit.dragToReorder')">
                <HolderOutlined class="text-sm" />
              </td>

              <!-- Code -->
              <td class="px-4 py-3.5 whitespace-nowrap">
                <span class="font-mono text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200/80 px-2 py-0.5 rounded">
                  {{ unit.code }}
                </span>
              </td>

              <!-- Name -->
              <td class="px-4 py-3.5">
                <div class="flex items-center gap-2">
                  <div class="w-6 h-6 rounded bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                    <TagOutlined class="text-xs" />
                  </div>
                  <span class="font-semibold text-slate-800">
                    {{ unit.name }}
                  </span>
                </div>
              </td>

              <!-- Description -->
              <td class="px-4 py-3.5 text-slate-600 text-xs">
                <span v-if="unit.description" class="line-clamp-2" :title="unit.description">
                  {{ unit.description }}
                </span>
                <span v-else class="text-slate-400">—</span>
              </td>

              <!-- Status Switch -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <a-switch
                  :checked="unit.is_active"
                  size="small"
                  @change="() => handleToggleStatus(unit)"
                  class="bg-slate-300"
                />
              </td>

              <!-- Actions -->
              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <div class="flex items-center justify-center gap-1">
                  <!-- Edit -->
                  <button
                    type="button"
                    @click="openEditModal(unit)"
                    class="w-7 h-7 rounded-lg text-slate-500 hover:text-sky-600 hover:bg-sky-50 flex items-center justify-center transition-colors cursor-pointer"
                    :title="t('unit.editBtn')"
                  >
                    <EditOutlined class="text-sm" />
                  </button>

                  <!-- Delete with Popconfirm placed to the left so it NEVER overflows -->
                  <a-popconfirm
                    :title="t('unit.confirmDelete')"
                    ok-text="Xóa"
                    cancel-text="Hủy"
                    ok-type="danger"
                    placement="left"
                    @confirm="() => handleDelete(unit)"
                  >
                    <button
                      type="button"
                      class="w-7 h-7 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer"
                      :title="t('unit.deleteBtn')"
                    >
                      <DeleteOutlined class="text-sm" />
                    </button>
                  </a-popconfirm>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div class="px-4 py-3 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
        <span class="text-xs text-slate-500">
          {{ t('unit.showingCount', { count: units.length, total: pagination.total }) }}
        </span>

        <a-pagination
          v-model:current="pagination.current"
          v-model:pageSize="pagination.pageSize"
          :total="pagination.total"
          :show-size-changer="true"
          :page-size-options="['10', '20', '50']"
          size="small"
          @change="handlePageChange"
        />
      </div>
    </div>

    <!-- Unit Form Modal -->
    <UnitFormModal
      v-model:visible="isModalVisible"
      :unit="selectedUnit"
      :existing-units="units"
      @success="handleModalSuccess"
    />
  </div>
</template>
