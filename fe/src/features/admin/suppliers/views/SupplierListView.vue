<script setup lang="ts">
import { ref, reactive, onMounted } from 'vue'
import { storeToRefs } from 'pinia'
import {
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  PlusOutlined,
  ShopOutlined,
  PhoneOutlined,
  MailOutlined,
  BankOutlined,
} from '@ant-design/icons-vue'
import type { Supplier, SupplierFilterParams } from '../types'
import { useSupplierStore } from '../stores/supplierStore'
import SupplierFormModal from '../components/SupplierFormModal.vue'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'

const toast = useToast()
const { t } = useI18n()
const breakpoints = useBreakpoints(breakpointsTailwind)
const isMobile = breakpoints.smaller('md')

const supplierStore = useSupplierStore()
const { suppliers, isLoading } = storeToRefs(supplierStore)

const filters = reactive<SupplierFilterParams>({
  search: '',
  is_active: '',
})

const pagination = reactive({
  current: 1,
  pageSize: 10,
  total: 0,
})

const isModalVisible = ref<boolean>(false)
const selectedSupplier = ref<Supplier | null>(null)
const modalMode = ref<'create' | 'edit' | 'view'>('create')

let debounceTimer: ReturnType<typeof setTimeout> | null = null

function getProvinceName(supplier: Supplier): string {
  return supplier.province?.name || ''
}

async function loadData() {
  try {
    const res = await supplierStore.fetchSuppliers({
      search: filters.search ? filters.search.trim() : undefined,
      is_active: filters.is_active !== '' && filters.is_active !== undefined ? filters.is_active : undefined,
      page: pagination.current,
      per_page: pagination.pageSize,
    })
    pagination.total = res.total
    pagination.current = res.current_page
    pagination.pageSize = res.per_page || pagination.pageSize
  } catch (err: any) {
    toast.error(err.message || t('supplier.loadError'))
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

function openCreateModal() {
  selectedSupplier.value = null
  modalMode.value = 'create'
  isModalVisible.value = true
}

function openEditModal(record: Supplier) {
  selectedSupplier.value = record
  modalMode.value = 'edit'
  isModalVisible.value = true
}

function openViewModal(record: Supplier) {
  selectedSupplier.value = record
  modalMode.value = 'view'
  isModalVisible.value = true
}

async function handleToggleStatus(record: Supplier) {
  try {
    await supplierStore.toggleStatus(record)
    toast.success(t('supplier.toggleStatusSuccess', { name: record.name }))
  } catch (err: any) {
    toast.error(err.message || t('supplier.toggleStatusError'))
  }
}

async function handleDelete(record: Supplier) {
  try {
    await supplierStore.deleteSupplier(record.id)
    toast.success(t('supplier.deleteSuccess', { name: record.name }))
    loadData()
  } catch (err: any) {
    const apiErrors = err.response?.data?.errors
    const firstError = apiErrors ? Object.values(apiErrors).flat()[0] : null
    toast.error(firstError || err.response?.data?.message || err.message || t('supplier.deleteError'))
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

    <!-- Page Header Bar: Balanced title + action -->
    <div class="flex items-center justify-between gap-3">
      <div>
        <h1 class="text-lg sm:text-xl font-bold text-slate-800 tracking-tight leading-tight">
          {{ t('admin.suppliers') }}
        </h1>
        <p class="text-xs text-slate-400 hidden sm:block mt-0.5">Danh sách các đối tác & nhà phân phối hàng hóa</p>
      </div>

      <button
        type="button"
        @click="openCreateModal"
        class="inline-flex items-center justify-center gap-1.5 px-3.5 sm:px-4 h-9 rounded-xl bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-xs sm:text-sm font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer select-none shrink-0"
      >
        <PlusOutlined class="text-xs text-white" />
        <span class="text-white leading-none">{{ t('supplier.addButton') }}</span>
      </button>
    </div>

    <div class="bg-white rounded-xl border border-slate-200/90 p-3 sm:p-3.5 shadow-2xs flex flex-wrap items-center gap-2 sm:gap-3">
      <div class="w-full sm:flex-1 sm:min-w-[200px] sm:max-w-xs md:max-w-sm">
        <a-input
          v-model:value="filters.search"
          :placeholder="t('supplier.searchPlaceholder')"
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
        :placeholder="t('supplier.filterStatus')"
        class="w-full sm:w-44 !rounded-lg !text-sm"
        allow-clear
        size="middle"
        @change="handleFilterChange"
      >
        <a-select-option value="">{{ t('supplier.allStatuses') }}</a-select-option>
        <a-select-option value="true">{{ t('supplier.statusActive') }}</a-select-option>
        <a-select-option value="false">{{ t('supplier.statusInactive') }}</a-select-option>
      </a-select>
    </div>

    <div v-if="isLoading && suppliers.length === 0" class="py-16 text-center bg-white rounded-xl border border-slate-200/90 shadow-2xs">
      <a-spin size="large" />
    </div>

    <div v-else-if="suppliers.length === 0" class="py-14 text-center bg-white rounded-xl border border-slate-200/90 p-6 shadow-2xs">
      <a-empty />
    </div>

    <div v-else class="bg-white rounded-xl border border-slate-200/90 shadow-2xs overflow-hidden">
      <!-- Desktop Table View (Tablet Landscape & Desktop) -->
      <div class="hidden md:block overflow-x-auto">
        <table class="w-full text-sm">
          <thead>
            <tr class="bg-slate-50/80 border-b border-slate-200/80">
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-12">{{ t('supplier.colSTT') }}</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider w-28">{{ t('supplier.colCode') }}</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider min-w-[220px]">{{ t('supplier.colName') }}</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ t('supplier.colPhone') }} / {{ t('supplier.colEmail') }}</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">{{ t('supplier.colBank') }}</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap min-w-[120px]">{{ t('supplier.colStatus') }}</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider whitespace-nowrap min-w-[90px]">{{ t('supplier.colActions') }}</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <tr
              v-for="(supplier, index) in suppliers"
              :key="supplier.id"
              class="hover:bg-slate-50/70 transition-colors"
            >

              <td class="px-4 py-3.5 text-slate-400 font-mono text-xs">
                {{ (pagination.current - 1) * pagination.pageSize + index + 1 }}
              </td>

              <td class="px-4 py-3.5 whitespace-nowrap">
                <span class="font-mono text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200/80 px-2 py-0.5 rounded">
                  {{ supplier.code }}
                </span>
              </td>

              <td class="px-4 py-3.5">
                <div class="cursor-pointer group" @click="openViewModal(supplier)">
                  <span class="font-semibold text-slate-800 group-hover:text-sky-600 transition-colors">
                    {{ supplier.name }}
                  </span>
                  <div class="flex items-center gap-2 mt-0.5 text-xs text-slate-400">
                    <span v-if="supplier.contact_person">
                      {{ t('supplier.contactLabel') }}: <strong class="text-slate-600 font-medium">{{ supplier.contact_person }}</strong>
                    </span>
                    <span v-if="supplier.contact_person && getProvinceName(supplier)">•</span>
                    <span v-if="getProvinceName(supplier)" class="text-slate-500">
                      {{ getProvinceName(supplier) }}
                    </span>
                  </div>
                </div>
              </td>

              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="space-y-0.5 text-xs">
                  <div v-if="supplier.phone" class="flex items-center gap-1.5 text-slate-700 font-medium">
                    <PhoneOutlined class="text-slate-400 text-[11px]" />
                    <a :href="`tel:${supplier.phone}`" class="hover:text-sky-600 hover:underline">{{ supplier.phone }}</a>
                  </div>
                  <div v-if="supplier.email" class="flex items-center gap-1.5 text-slate-500">
                    <MailOutlined class="text-slate-400 text-[11px]" />
                    <a :href="`mailto:${supplier.email}`" class="hover:text-sky-600 hover:underline truncate max-w-[170px] inline-block">{{ supplier.email }}</a>
                  </div>
                  <span v-if="!supplier.phone && !supplier.email" class="text-slate-400">—</span>
                </div>
              </td>

              <td class="px-4 py-3.5 whitespace-nowrap">
                <div v-if="supplier.bank_account || supplier.bank_name" class="text-xs space-y-0.5">
                  <div class="font-mono font-medium text-slate-800 flex items-center gap-1">
                    <BankOutlined class="text-amber-500 text-[11px]" />
                    <span>{{ supplier.bank_account || '—' }}</span>
                  </div>
                  <div class="text-slate-400 truncate max-w-[160px]" :title="supplier.bank_name || ''">
                    {{ supplier.bank_name || '—' }}
                  </div>
                </div>
                <span v-else class="text-xs text-slate-400">—</span>
              </td>

              <td class="px-4 py-3.5 text-center whitespace-nowrap">
                <a-switch
                  :checked="supplier.is_active"
                  size="small"
                  @change="handleToggleStatus(supplier)"
                />
              </td>

              <td class="px-4 py-3.5 whitespace-nowrap">
                <div class="flex items-center justify-center gap-1">

                  <button
                    type="button"
                    @click="openEditModal(supplier)"
                    class="w-8 h-8 rounded-lg hover:bg-slate-200/70 text-slate-400 hover:text-slate-800 flex items-center justify-center transition-colors cursor-pointer touch-target-min"
                    :title="t('supplier.editBtn')"
                  >
                    <EditOutlined class="text-sm" />
                  </button>

                  <a-popconfirm
                    :title="t('supplier.confirmDelete')"
                    :ok-text="t('supplier.deleteBtn')"
                    :cancel-text="t('supplier.cancelBtn')"
                    ok-type="danger"
                    placement="left"
                    @confirm="handleDelete(supplier)"
                  >
                    <button
                      type="button"
                      class="w-8 h-8 rounded-lg hover:bg-rose-50 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer touch-target-min"
                      :title="t('supplier.deleteBtn')"
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

      <!-- Mobile & Tablet Portrait Cards (< 768px): Modern & Ergonomic -->
      <div class="md:hidden divide-y divide-slate-100">
        <div
          v-for="supplier in suppliers"
          :key="supplier.id"
          class="p-3.5 sm:p-4 hover:bg-slate-50/80 transition-colors flex flex-col gap-2.5"
        >
          <!-- Top Row: Code + Name + Status Switch -->
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2 min-w-0">
              <span class="font-mono text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200/90 px-2 py-0.5 rounded-md shrink-0">
                {{ supplier.code }}
              </span>
              <span
                class="font-bold text-slate-800 text-sm hover:text-sky-600 cursor-pointer truncate"
                @click="openViewModal(supplier)"
              >
                {{ supplier.name }}
              </span>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              <span class="text-[11px] font-medium" :class="supplier.is_active ? 'text-emerald-600' : 'text-slate-400'">
                {{ supplier.is_active ? 'Hoạt động' : 'Tắt' }}
              </span>
              <a-switch
                :checked="supplier.is_active"
                size="small"
                @change="handleToggleStatus(supplier)"
              />
            </div>
          </div>

          <!-- Metadata Box -->
          <div class="text-xs text-slate-600 space-y-1.5 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
            <div v-if="supplier.contact_person || getProvinceName(supplier)" class="flex items-center gap-1.5">
              <span v-if="supplier.contact_person">LH: <strong class="text-slate-800">{{ supplier.contact_person }}</strong></span>
              <span v-if="supplier.contact_person && getProvinceName(supplier)">•</span>
              <span v-if="getProvinceName(supplier)" class="text-slate-500">{{ getProvinceName(supplier) }}</span>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-[11px]">
              <a v-if="supplier.phone" :href="`tel:${supplier.phone}`" class="text-sky-600 font-medium flex items-center gap-1 hover:underline">
                <PhoneOutlined class="text-[10px]" /> {{ supplier.phone }}
              </a>
              <span v-if="supplier.bank_account" class="text-slate-600 font-mono flex items-center gap-1">
                <BankOutlined class="text-amber-500 text-[10px]" /> {{ supplier.bank_account }}
              </span>
            </div>
          </div>

          <!-- Bottom Action Bar: Icon buttons on right -->
          <div class="flex items-center justify-end pt-2 border-t border-slate-100 gap-1.5">
            <button
              type="button"
              @click="openEditModal(supplier)"
              class="w-8 h-8 rounded-lg bg-white border border-slate-200/90 hover:bg-sky-50 hover:border-sky-300 text-slate-600 hover:text-sky-600 flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
              :title="t('supplier.editBtn')"
            >
              <EditOutlined class="text-sm" />
            </button>

            <a-popconfirm
              :title="t('supplier.confirmDelete')"
              :ok-text="t('supplier.deleteBtn')"
              :cancel-text="t('supplier.cancelBtn')"
              ok-type="danger"
              placement="left"
              @confirm="handleDelete(supplier)"
            >
              <button
                type="button"
                class="w-8 h-8 rounded-lg bg-white border border-slate-200/90 hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
                :title="t('supplier.deleteBtn')"
              >
                <DeleteOutlined class="text-sm" />
              </button>
            </a-popconfirm>
          </div>
        </div>
      </div>

      <div
        v-if="pagination.total > 0"
        class="border-t border-slate-100 px-4 py-3 flex flex-col sm:flex-row items-center justify-between gap-3"
      >
        <span class="text-xs sm:text-sm text-slate-500 text-center sm:text-left">
          {{ t('supplier.showingCount', { count: suppliers.length, total: pagination.total }) }}
        </span>
        <a-pagination
          v-model:current="pagination.current"
          v-model:pageSize="pagination.pageSize"
          :total="pagination.total"
          :page-size-options="['10', '20', '50', '100']"
          :show-size-changer="!isMobile"
          size="small"
          class="flex justify-center sm:justify-end"
          @change="handlePageChange"
        />
      </div>
    </div>

    <SupplierFormModal
      v-model:visible="isModalVisible"
      :supplier="selectedSupplier"
      :mode="modalMode"
      @success="handleModalSuccess"
    />
  </div>
</template>
