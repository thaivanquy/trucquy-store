<script setup lang="ts">
import { ref, reactive, computed, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import {
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  PictureOutlined,
  PlusOutlined,
  DownOutlined,
  RightOutlined,
  FolderAddOutlined,
  HolderOutlined,
} from '@ant-design/icons-vue'
import type { Category, CategoryFilterParams } from '../types'
import { useCategoryStore } from '../stores/categoryStore'
import CategoryFormModal from '../components/CategoryFormModal.vue'
import CategoryDetailDrawer from '../components/CategoryDetailDrawer.vue'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'

const toast = useToast()
const { t } = useI18n()

const categoryStore = useCategoryStore()
const { allCategories, rootCategories, isLoading } = storeToRefs(categoryStore)

const filters = reactive<CategoryFilterParams>({
  search: '',
  parent_id: '',
  is_active: '',
})

const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0,
})

const expandedParentIds = ref<Set<number>>(new Set())

interface CategoryTreeGroup {
  parent: Category
  children: Category[]
}

const treeGroups = ref<CategoryTreeGroup[]>([])

const isModalVisible = ref<boolean>(false)
const selectedCategory = ref<Category | null>(null)
const defaultParentId = ref<number | null>(null)

const isDetailDrawerVisible = ref<boolean>(false)
const selectedDetailCategory = ref<Category | null>(null)

function openDetailDrawer(cat: Category) {
  selectedDetailCategory.value = cat
  isDetailDrawerVisible.value = true
}

let debounceTimer: ReturnType<typeof setTimeout> | null = null

const draggedIndex = ref<number | null>(null)
const isReordering = ref<boolean>(false)

async function loadDropdownCategories() {
  try {
    await categoryStore.fetchDropdownCategories()
  } catch (err: any) {
    console.error(err)
  }
}

async function loadData() {
  try {
    const response = await categoryStore.fetchCategories({
      tree: true,
      search: filters.search ? filters.search.trim() : undefined,
      parent_id: filters.parent_id !== '' && filters.parent_id !== undefined ? filters.parent_id : undefined,
      is_active: filters.is_active !== '' && filters.is_active !== undefined ? filters.is_active : undefined,
      page: pagination.current,
      per_page: pagination.pageSize,
    })
    treeGroups.value = response.data.map((cat) => ({
      parent: cat,
      children: cat.children || [],
    }))

    pagination.total = response.total
    pagination.current = response.current_page
    pagination.pageSize = response.per_page || pagination.pageSize

    treeGroups.value.forEach((g) => {
      if (!expandedParentIds.value.has(g.parent.id)) {
        expandedParentIds.value.add(g.parent.id)
      }
    })
  } catch (err: any) {
    toast.error(err.message || t('category.loadError'))
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

function toggleExpand(parentId: number) {
  if (expandedParentIds.value.has(parentId)) {
    expandedParentIds.value.delete(parentId)
  } else {
    expandedParentIds.value.add(parentId)
  }
}

function expandAll() {
  treeGroups.value.forEach((g) => expandedParentIds.value.add(g.parent.id))
}

function collapseAll() {
  expandedParentIds.value.clear()
}

const isAllExpanded = computed(() => {
  if (treeGroups.value.length === 0) return false
  return treeGroups.value.every((g) => expandedParentIds.value.has(g.parent.id))
})

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

  const movedGroup = treeGroups.value.splice(draggedIndex.value, 1)[0]
  if (movedGroup) {
    treeGroups.value.splice(targetIndex, 0, movedGroup)

    const items = treeGroups.value.map((g, idx) => ({
      id: g.parent.id,
      sort_order: (pagination.current - 1) * pagination.pageSize + idx + 1,
    }))

    isReordering.value = true
    try {
      await categoryStore.reorderCategories(items)
      toast.success(t('category.reorderSuccess'))
    } catch (err: any) {
      toast.error(err.message || t('category.reorderError'))
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

function openCreateRootModal() {
  selectedCategory.value = null
  defaultParentId.value = null
  isModalVisible.value = true
}

function openCreateSubModal(parent: Category) {
  selectedCategory.value = null
  defaultParentId.value = parent.id
  isModalVisible.value = true
  expandedParentIds.value.add(parent.id)
}

function openEditModal(record: Category) {
  selectedCategory.value = record
  defaultParentId.value = record.parent_id
  isModalVisible.value = true
}

async function handleToggleStatus(record: Category) {
  try {
    await categoryStore.toggleStatus(record)
    toast.success(t('category.toggleStatusSuccess', { name: record.name }))
    loadDropdownCategories()
  } catch (err: any) {
    toast.error(err.message || t('category.toggleStatusError'))
  }
}

async function handleDelete(record: Category) {
  try {
    await categoryStore.deleteCategory(record.id)
    toast.success(t('category.deleteSuccess', { name: record.name }))
    loadData()
    loadDropdownCategories()
  } catch (err: any) {
    const apiErrors = err.response?.data?.errors
    const firstError = apiErrors ? Object.values(apiErrors).flat()[0] : null
    toast.error(firstError || err.response?.data?.message || err.message || t('category.deleteError'))
  }
}

function handleModalSuccess() {
  loadData()
  loadDropdownCategories()
}

function resetFilters() {
  filters.search = ''
  filters.parent_id = ''
  filters.is_active = ''
  pagination.current = 1
  loadData()
}

onMounted(() => {
  loadData()
  loadDropdownCategories()
})
</script>

<template>
  <div class="space-y-4 pb-12 sm:pb-16 overflow-x-hidden">
    <!-- Page Title & Header Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-0.5">
      <div>
        <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">{{ t('category.title') }}</h1>
        <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Quản lý cấu trúc danh mục cha con và thứ tự hiển thị</p>
      </div>

      <div class="flex items-center gap-2 self-start sm:self-auto">
        <button
          type="button"
          @click="isAllExpanded ? collapseAll() : expandAll()"
          class="px-3 h-9 rounded-lg border border-slate-200/90 bg-white hover:bg-slate-50 text-slate-700 text-xs sm:text-sm font-medium shadow-2xs transition-colors cursor-pointer flex items-center gap-1.5"
        >
          <component :is="isAllExpanded ? DownOutlined : RightOutlined" class="text-xs text-slate-400" />
          <span>{{ isAllExpanded ? t('category.collapseAll') : t('category.expandAll') }}</span>
        </button>

        <button
          type="button"
          @click="openCreateRootModal"
          class="inline-flex items-center justify-center gap-1.5 px-3.5 sm:px-4 h-9 rounded-lg bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-xs sm:text-sm font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer select-none"
        >
          <PlusOutlined class="text-xs text-white" />
          <span class="text-white leading-none">{{ t('category.addRoot') }}</span>
        </button>
      </div>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/90 p-3 sm:p-3.5 shadow-2xs flex flex-wrap items-center gap-2 sm:gap-3">
      <div class="w-full sm:flex-1 sm:min-w-[200px] sm:max-w-xs md:max-w-sm">
        <a-input
          v-model:value="filters.search"
          :placeholder="t('category.searchPlaceholder')"
          allow-clear
          size="middle"
          class="!rounded-lg !text-sm !bg-slate-50/70 hover:!bg-white focus:!bg-white transition-colors"
          @input="handleSearchInput"
          @change="handleSearchInput"
        >
          <template #prefix><SearchOutlined class="text-slate-400 text-sm" /></template>
        </a-input>
      </div>

      <div class="flex items-center gap-2 w-full sm:w-auto">
        <a-select
          v-model:value="filters.parent_id"
          :placeholder="t('category.filterParent')"
          class="flex-1 sm:w-52 !rounded-lg !text-sm"
          allow-clear
          size="middle"
          @change="handleFilterChange"
        >
          <a-select-option value="">{{ t('category.allParents') }}</a-select-option>
          <a-select-option
            v-for="root in rootCategories"
            :key="root.id"
            :value="root.id"
          >
            {{ root.name }} ({{ root.code }})
          </a-select-option>
        </a-select>

        <a-select
          v-model:value="filters.is_active"
          :placeholder="t('category.filterStatus')"
          class="flex-1 sm:w-36 !rounded-lg !text-sm"
          allow-clear
          size="middle"
          @change="handleFilterChange"
        >
          <a-select-option value="">{{ t('category.allStatuses') }}</a-select-option>
          <a-select-option value="true">{{ t('category.statusActive') }}</a-select-option>
          <a-select-option value="false">{{ t('category.statusInactive') }}</a-select-option>
        </a-select>
      </div>
    </div>

    <div v-if="isLoading && treeGroups.length === 0" class="py-16 text-center bg-white rounded-xl border border-slate-200/90 shadow-2xs">
      <a-spin size="large" />
    </div>

    <div v-else-if="treeGroups.length === 0" class="py-14 text-center bg-white rounded-xl border border-slate-200/90 p-6 shadow-2xs">
      <a-empty />
    </div>

    <div v-else class="space-y-3.5">
      <div
        v-for="(group, index) in treeGroups"
        :key="group.parent.id"
        draggable="true"
        @dragstart="onDragStart(index, $event)"
        @dragover.prevent="onDragOver(index)"
        @drop="onDrop(index)"
        @dragend="onDragEnd"
        class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden transition-all duration-200"
        :class="{ 'opacity-40 ring-2 ring-sky-500 scale-[0.99]': draggedIndex === index }"
      >
        <div
          class="px-4 py-3.5 sm:py-4 bg-slate-50/70 hover:bg-slate-50 flex flex-col sm:flex-row sm:items-start justify-between gap-3 select-none transition-colors border-b border-slate-100"
        >
          <div
            class="flex items-start gap-3.5 min-w-0 flex-1 cursor-pointer"
            @click="toggleExpand(group.parent.id)"
          >
            <div
              class="w-7 h-7 mt-1 rounded-md flex items-center justify-center text-slate-400 hover:text-slate-700 hover:bg-slate-200/60 shrink-0 cursor-grab active:cursor-grabbing transition-colors"
              :title="t('category.dragToReorder')"
              @click.stop
            >
              <HolderOutlined class="text-base" />
            </div>

            <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl overflow-hidden shrink-0 border border-slate-200 bg-slate-100 shadow-2xs flex items-center justify-center">
              <img
                v-if="group.parent.image"
                :src="group.parent.image"
                :alt="group.parent.name"
                class="w-full h-full object-cover"
              />
              <PictureOutlined v-else class="text-slate-400 text-xl" />
            </div>

            <div class="min-w-0 flex-1 pt-0.5">
              <div class="flex items-center gap-2.5 flex-wrap">
                <span
                  class="font-bold text-slate-900 text-base sm:text-lg leading-tight hover:text-sky-600 transition-colors cursor-pointer"
                  @click.stop="openDetailDrawer(group.parent)"
                  :title="'Xem chi tiết: ' + group.parent.name"
                >
                  {{ group.parent.name }}
                </span>
                <span class="font-mono text-xs sm:text-sm font-bold text-sky-700 bg-sky-50 border border-sky-200 px-2.5 py-0.5 rounded">
                  {{ group.parent.code }}
                </span>
                <span class="text-xs text-slate-400 font-mono">
                  /{{ group.parent.slug }}
                </span>
                <span class="text-xs font-semibold text-slate-600 bg-slate-200/80 px-2.5 py-0.5 rounded-full">
                  {{ t('category.subCategoriesCount', { count: group.children.length }) }}
                </span>
              </div>
              <p v-if="group.parent.description" class="text-sm text-slate-500 line-clamp-2 mt-1">
                {{ group.parent.description }}
              </p>
            </div>
          </div>

          <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-1 border-t sm:border-t-0 border-slate-200/60">
            <div class="flex items-center gap-2">
              <span class="text-sm font-medium text-slate-600">
                {{ group.parent.is_active ? t('category.on') : t('category.off') }}
              </span>
              <a-switch
                :checked="group.parent.is_active"
                size="small"
                @change="handleToggleStatus(group.parent)"
              />
            </div>

            <div class="h-4 w-px bg-slate-200"></div>

            <button
              type="button"
              @click.stop="openCreateSubModal(group.parent)"
              class="px-3 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs sm:text-sm font-semibold border border-sky-200/90 transition-colors cursor-pointer flex items-center gap-1.5"
              :title="t('category.addSub')"
            >
              <FolderAddOutlined class="text-xs" />
              <span>{{ t('category.addSub') }}</span>
            </button>

            <button
              type="button"
              @click.stop="openEditModal(group.parent)"
              class="w-8 h-8 rounded-lg hover:bg-slate-200/70 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors cursor-pointer"
              :title="t('category.editParent')"
            >
              <EditOutlined class="text-sm" />
            </button>

            <a-popconfirm
              v-if="group.children.length === 0"
              :title="t('category.confirmDeleteParent')"
              :ok-text="t('category.deleteBtn')"
              :cancel-text="t('category.cancelBtn')"
              ok-type="danger"
              placement="left"
              @confirm="handleDelete(group.parent)"
            >
              <button
                type="button"
                class="w-8 h-8 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                :title="t('category.deleteParent')"
              >
                <DeleteOutlined class="text-sm" />
              </button>
            </a-popconfirm>

            <button
              v-else
              type="button"
              class="w-8 h-8 rounded-lg text-slate-300 hover:text-slate-400 flex items-center justify-center transition-colors cursor-not-allowed"
              :title="t('category.cannotDeleteTooltip')"
              @click="toast.warning(t('category.cannotDeleteHasChildren'))"
            >
              <DeleteOutlined class="text-sm" />
            </button>
          </div>
        </div>

        <div
          v-if="expandedParentIds.has(group.parent.id)"
          class="p-3 sm:p-4 bg-white"
        >
          <div v-if="group.children.length === 0" class="py-4 px-4 text-center border border-dashed border-slate-200 rounded-lg text-slate-400 text-sm">
            {{ t('category.noSubCategories') }} <button type="button" @click="openCreateSubModal(group.parent)" class="text-sky-600 font-semibold hover:underline cursor-pointer">[{{ t('category.addSub') }}]</button> {{ t('category.toCreateSub') }}
          </div>

          <div v-else class="space-y-2.5 ml-2 sm:ml-8 pl-2.5 sm:pl-5 border-l-2 border-sky-200">
            <div
              v-for="child in group.children"
              :key="child.id"
              class="group relative bg-slate-50/70 hover:bg-slate-100/80 rounded-xl p-3 sm:p-3.5 border border-slate-200/70 transition-all flex flex-col sm:flex-row sm:items-start justify-between gap-2.5"
            >
              <div class="flex items-start gap-3 min-w-0 flex-1">
                <span class="text-sky-400 font-mono text-sm shrink-0 select-none mt-1">└─</span>

                <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-lg overflow-hidden shrink-0 border border-slate-200 bg-slate-100 shadow-2xs flex items-center justify-center">
                  <img
                    v-if="child.image"
                    :src="child.image"
                    :alt="child.name"
                    class="w-full h-full object-cover"
                  />
                  <PictureOutlined v-else class="text-slate-400 text-sm" />
                </div>

                <div class="min-w-0 flex-1 pt-0.5">
                  <div class="flex items-center gap-2.5 flex-wrap">
                    <span
                      class="font-semibold text-slate-800 text-sm sm:text-base hover:text-sky-600 transition-colors cursor-pointer"
                      @click.stop="openDetailDrawer(child)"
                      :title="'Xem chi tiết: ' + child.name"
                    >
                      {{ child.name }}
                    </span>
                    <span class="font-mono text-xs font-semibold text-slate-700 bg-white border border-slate-200 px-2 py-0.5 rounded">
                      {{ child.code }}
                    </span>
                    <span class="text-xs text-slate-400 font-mono">
                      /{{ child.slug }}
                    </span>
                  </div>
                  <p v-if="child.description" class="text-sm text-slate-500 line-clamp-2 mt-0.5">
                    {{ child.description }}
                  </p>
                </div>
              </div>

              <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-1 border-t sm:border-t-0 border-slate-200/60 pl-6 sm:pl-0">
                <div class="flex items-center gap-2">
                  <span class="text-xs sm:text-sm text-slate-500 font-medium">
                    {{ child.is_active ? t('category.on') : t('category.off') }}
                  </span>
                  <a-switch
                    :checked="child.is_active"
                    size="small"
                    @change="handleToggleStatus(child)"
                  />
                </div>

                <div class="flex items-center gap-1">
                  <button
                    type="button"
                    @click="openEditModal(child)"
                    class="w-7 h-7 rounded-md hover:bg-slate-200/80 text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors cursor-pointer"
                    :title="t('category.editSub')"
                  >
                    <EditOutlined class="text-xs" />
                  </button>

                  <a-popconfirm
                    :title="t('category.confirmDeleteSub')"
                    :ok-text="t('category.deleteBtn')"
                    :cancel-text="t('category.cancelBtn')"
                    ok-type="danger"
                    placement="left"
                    @confirm="handleDelete(child)"
                  >
                    <button
                      type="button"
                      class="w-7 h-7 rounded-md hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer"
                      :title="t('category.deleteSub')"
                    >
                      <DeleteOutlined class="text-xs" />
                    </button>
                  </a-popconfirm>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div
        v-if="pagination.total > 0"
        class="bg-white rounded-xl border border-slate-200/90 px-4 py-3 shadow-2xs flex flex-col sm:flex-row sm:items-center justify-between gap-3 mt-4"
      >
        <span class="text-xs text-slate-500 text-center sm:text-left">
          {{ t('category.showingCount', { count: treeGroups.length, total: pagination.total }) }}
        </span>
        <a-pagination
          v-model:current="pagination.current"
          v-model:pageSize="pagination.pageSize"
          :total="pagination.total"
          :page-size-options="['5', '10', '20', '50']"
          :show-size-changer="true"
          size="small"
          class="flex justify-center sm:justify-end"
          @change="handlePageChange"
        />
      </div>
    </div>

    <CategoryFormModal
      v-model:visible="isModalVisible"
      :category="selectedCategory"
      :categories="allCategories"
      :default-parent-id="defaultParentId"
      @success="handleModalSuccess"
    />

    <CategoryDetailDrawer
      v-model:visible="isDetailDrawerVisible"
      :category="selectedDetailCategory"
      @edit="openEditModal"
    />
  </div>
</template>
