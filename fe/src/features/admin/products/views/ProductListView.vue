<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { storeToRefs } from 'pinia'
import {
  SearchOutlined,
  EditOutlined,
  DeleteOutlined,
  PlusOutlined,
  PictureOutlined,
  BarcodeOutlined,
  InboxOutlined,
  CopyOutlined,
} from '@ant-design/icons-vue'
import type { Product, ProductFilterParams } from '../types'
import { useProductStore } from '../stores/productStore'
import { useCategoryStore } from '@/features/admin/categories/stores/categoryStore'
import ProductFormModal from '../components/ProductFormModal.vue'
import ProductDetailModal from '../components/ProductDetailModal.vue'
import { formatPrice, calculateMargin, copyToClipboard } from '@/utils/format'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'

const toast = useToast()
const { t } = useI18n()
const breakpoints = useBreakpoints(breakpointsTailwind)
const isMobile = breakpoints.smaller('md')

const productStore = useProductStore()
const { products, isLoading } = storeToRefs(productStore)
const categoryStore = useCategoryStore()

const filters = reactive<{
  search: string
  category_id: string | number
  stock_status: string
}>({
  search: '',
  category_id: '',
  stock_status: 'all',
})

const pagination = reactive({
  current: 1,
  pageSize: 20,
  total: 0,
})

const isModalVisible = ref<boolean>(false)
const selectedProduct = ref<Product | null>(null)

const isDetailModalVisible = ref<boolean>(false)
const selectedDetailProduct = ref<Product | null>(null)

const isBarcodeModalVisible = ref<boolean>(false)
const selectedBarcodeProduct = ref<Product | null>(null)
const activeBarcodeItem = ref<{
  name: string
  barcode: string
  conversionRate?: number
} | null>(null)

function selectBarcodeUnit(name: string, barcode: string, conversionRate: number = 1) {
  activeBarcodeItem.value = {
    name,
    barcode,
    conversionRate,
  }
}

const conversionUnitsWithBarcodes = computed(() => {
  if (!selectedBarcodeProduct.value?.product_units) return []
  return selectedBarcodeProduct.value.product_units.filter(
    (u) => !!u.barcode && u.barcode !== selectedBarcodeProduct.value?.barcode && !u.is_base_unit
  )
})

let debounceTimer: ReturnType<typeof setTimeout> | null = null

function getStockPercentage(product: Product): number {
  if (!product.stock_quantity || product.stock_quantity <= 0) return 0
  const maxBenchmark = Math.max(product.min_stock * 3, 20)
  return Math.min(100, Math.round((product.stock_quantity / maxBenchmark) * 100))
}

async function loadData() {
  try {
    const params: ProductFilterParams = {
      search: filters.search ? filters.search.trim() : undefined,
      category_id: filters.category_id !== '' && filters.category_id !== undefined ? filters.category_id : undefined,
      page: pagination.current,
      per_page: pagination.pageSize,
      sort_by: 'id',
      sort_direction: 'desc',
    }

    if (filters.stock_status === 'low_stock') {
      params.low_stock = true
    } else if (filters.stock_status === 'out_of_stock') {
      params.out_of_stock = true
    }

    const res = await productStore.fetchProducts(params)
    pagination.total = res.total
    pagination.current = res.current_page
    pagination.pageSize = res.per_page || pagination.pageSize
  } catch (err: any) {
    toast.error(err.message || t('product.loadError'))
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
  selectedProduct.value = null
  isModalVisible.value = true
}

function openEditModal(record: Product) {
  selectedProduct.value = record
  isModalVisible.value = true
}

function openDetailModal(record: Product) {
  selectedDetailProduct.value = record
  isDetailModalVisible.value = true
}

function openBarcodeModal(record: Product) {
  selectedBarcodeProduct.value = record
  activeBarcodeItem.value = {
    name: record.unit?.name ? `${record.unit.name} (ĐVT cơ bản)` : 'ĐVT cơ bản',
    barcode: record.barcode || '',
    conversionRate: 1,
  }
  isBarcodeModalVisible.value = true
}

async function handleDelete(record: Product) {
  try {
    await productStore.deleteProduct(record.id)
    toast.success(t('product.deleteSuccess', { name: record.name }))
    loadData()
  } catch (err: any) {
    const apiErrors = err.response?.data?.errors
    const firstError = apiErrors ? Object.values(apiErrors).flat()[0] : null
    toast.error(firstError || err.response?.data?.message || err.message || t('product.deleteError'))
  }
}

function handleModalSuccess() {
  loadData()
}

onMounted(() => {
  loadData()
  if (categoryStore.allCategories.length === 0) {
    categoryStore.fetchDropdownCategories()
  }
})
</script>

<template>
  <div class="space-y-4 pb-12 sm:pb-16 overflow-x-hidden">

    <!-- Adaptive Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-3 sm:p-3.5 shadow-2xs flex flex-wrap items-center gap-2 sm:gap-3">

      <div class="w-full sm:flex-1 sm:min-w-[200px] sm:max-w-sm">
        <a-input
          v-model:value="filters.search"
          :placeholder="t('product.searchPlaceholder')"
          allow-clear
          size="middle"
          class="!rounded-xl !text-sm !bg-slate-50/70 hover:!bg-white focus:!bg-white transition-colors"
          @input="handleSearchInput"
          @change="handleSearchInput"
        >
          <template #prefix><SearchOutlined class="text-slate-400 text-sm" /></template>
        </a-input>
      </div>

      <div class="flex items-center gap-2 w-full sm:w-auto">
        <a-select
          v-model:value="filters.category_id"
          :placeholder="t('product.filterCategory')"
          class="flex-1 sm:w-44 !rounded-xl !text-xs sm:!text-sm"
          allow-clear
          size="middle"
          @change="handleFilterChange"
        >
          <a-select-option value="">{{ t('product.allCategories') }}</a-select-option>
          <a-select-option
            v-for="cat in categoryStore.allCategories"
            :key="cat.id"
            :value="cat.id"
          >
            {{ cat.name }}
          </a-select-option>
        </a-select>

        <a-select
          v-model:value="filters.stock_status"
          placeholder="Tình trạng tồn kho"
          class="flex-1 sm:w-40 !rounded-xl !text-xs sm:!text-sm"
          size="middle"
          @change="handleFilterChange"
        >
          <a-select-option value="all">Tất cả tồn kho</a-select-option>
          <a-select-option value="low_stock">Sắp hết hàng</a-select-option>
          <a-select-option value="out_of_stock">Đã hết hàng</a-select-option>
        </a-select>
      </div>

      <div class="w-full sm:w-auto sm:ml-auto flex items-center shrink-0 pt-1 sm:pt-0">
        <button
          type="button"
          @click="openCreateModal"
          class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 h-9 rounded-xl bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-sm font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer select-none touch-target-min"
        >
          <PlusOutlined class="text-xs text-white" />
          <span class="text-white leading-none">{{ t('product.addButton') }}</span>
        </button>
      </div>
    </div>

    <div v-if="isLoading && products.length === 0" class="py-20 text-center bg-white rounded-2xl border border-slate-200/90 shadow-2xs">
      <a-spin size="large" />
    </div>

    <div v-else-if="products.length === 0" class="py-14 text-center bg-white rounded-2xl border border-slate-200/90 p-6 shadow-2xs">
      <a-empty />
    </div>

    <div
      v-else
      class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-2.5 sm:gap-4"
    >
      <div
        v-for="product in products"
        :key="product.id"
        class="bg-white rounded-xl sm:rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-lg hover:border-sky-300/80 transition-all duration-200 flex flex-col overflow-hidden group"
      >

        <div
          class="relative aspect-[4/3] sm:aspect-[16/11] bg-slate-100 overflow-hidden select-none cursor-pointer"
          @click="openDetailModal(product)"
        >
          <img
            v-if="product.image"
            :src="product.image"
            :alt="product.name"
            class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            loading="lazy"
          />
          <div
            v-else
            class="w-full h-full flex flex-col items-center justify-center text-slate-300 bg-gradient-to-br from-slate-50 to-slate-100 group-hover:text-slate-400 transition-colors"
          >
            <PictureOutlined class="text-2xl sm:text-3xl mb-1 opacity-70" />
            <span class="text-xs font-medium text-slate-400">Chưa có ảnh</span>
          </div>

          <!-- Category floating pill -->
          <div class="absolute top-1.5 left-1.5 sm:top-2.5 sm:left-2.5 z-10">
            <span
              v-if="product.category?.name"
              class="inline-flex items-center gap-1 bg-white/95 backdrop-blur-md px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-md sm:rounded-lg text-xs font-semibold text-slate-700 shadow-2xs border border-white/60 truncate max-w-[120px] sm:max-w-none"
            >
              {{ product.category.name }}
            </span>
          </div>

          <!-- Stock Status Badge -->
          <div class="absolute bottom-1.5 left-1.5 right-1.5 sm:bottom-2 sm:left-2.5 sm:right-2.5 z-10 flex items-center justify-between">
            <span
              v-if="product.stock_quantity <= 0"
              class="inline-flex items-center bg-rose-600/95 backdrop-blur-md text-white text-xs font-bold px-2 py-0.5 rounded-md shadow-xs"
            >
              Hết hàng
            </span>
            <span
              v-else-if="product.stock_quantity <= product.min_stock"
              class="inline-flex items-center bg-amber-500/95 backdrop-blur-md text-white text-xs font-bold px-2 py-0.5 rounded-md shadow-xs truncate"
            >
              Còn {{ product.stock_quantity }}
            </span>
            <span
              v-else
              class="inline-flex items-center bg-slate-900/70 backdrop-blur-md text-white text-xs font-medium px-2 py-0.5 rounded-md shadow-xs truncate"
            >
              Còn {{ product.stock_quantity }}
            </span>
          </div>
        </div>

        <!-- Product Card Body -->
        <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between space-y-2 sm:space-y-3">
          <div>
            <!-- SKU & Barcode - always visible on all devices -->
            <div class="flex flex-wrap items-center justify-between gap-1.5 mb-1.5">
              <span class="font-mono text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200/90 px-2 py-0.5 rounded shrink-0">
                {{ product.code }}
              </span>
              <span
                v-if="product.barcode"
                @click.stop="openBarcodeModal(product)"
                class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-sky-50 hover:text-sky-700 hover:border-sky-300 border border-slate-200/90 px-2 py-0.5 rounded tracking-tight shadow-2xs cursor-pointer transition-colors"
                :title="'Bấm để xem thông tin mã vạch: ' + product.barcode"
              >
                <BarcodeOutlined class="text-slate-500 text-xs" />
                <span class="truncate max-w-[120px] sm:max-w-none">{{ product.barcode }}</span>
              </span>
            </div>

            <!-- Title: Click to open detail modal -->
            <h4
              class="font-semibold text-slate-800 hover:text-sky-600 transition-colors line-clamp-2 text-sm leading-snug cursor-pointer min-h-[38px]"
              @click="openDetailModal(product)"
              :title="'Bấm để xem chi tiết: ' + product.name"
            >
              {{ product.name }}
            </h4>

            <div v-if="product.supplier?.name" class="mt-1 text-xs text-slate-400 truncate" :title="product.supplier.name">
              NCC: {{ product.supplier.name }}
            </div>
          </div>

          <div class="pt-2 border-t border-slate-100 space-y-2">
            <!-- Pricing -->
            <div class="flex items-baseline justify-between gap-1">
              <div>
                <span class="text-xs text-slate-400 block leading-tight">Giá bán</span>
                <div class="flex items-baseline gap-1 flex-wrap">
                  <span class="text-base sm:text-lg font-bold text-slate-900 tracking-tight">
                    {{ formatPrice(product.selling_price) }}
                  </span>
                  <span v-if="product.unit?.name" class="text-xs text-slate-500 font-medium">
                    / {{ product.unit.name }}
                  </span>
                </div>
              </div>

              <div v-if="product.cost_price > 0" class="text-right hidden xs:block">
                <span class="text-xs text-slate-400 block leading-tight">Giá vốn</span>
                <span class="text-xs font-semibold text-slate-600">
                  {{ formatPrice(product.cost_price) }}
                </span>
              </div>
            </div>

            <!-- Inventory bar -->
            <div>
              <div class="flex items-center justify-between text-xs mb-1">
                <span class="text-slate-400">Tồn</span>
                <span class="font-semibold text-slate-700">
                  {{ product.stock_quantity }} / {{ product.min_stock }}
                </span>
              </div>
              <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                <div
                  class="h-full rounded-full transition-all duration-300"
                  :class="[
                    product.stock_quantity <= 0
                      ? 'bg-rose-500 w-0'
                      : product.stock_quantity <= product.min_stock
                        ? 'bg-amber-500'
                        : 'bg-emerald-500'
                  ]"
                  :style="{ width: `${getStockPercentage(product)}%` }"
                ></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Card Action Footer: Icon buttons on right -->
        <div class="px-3 py-2 bg-slate-50/80 border-t border-slate-100 flex items-center justify-end gap-1.5">
          <button
            type="button"
            @click.stop="openEditModal(product)"
            class="w-8 h-8 rounded-lg bg-white border border-slate-200/90 hover:bg-sky-50 hover:border-sky-300 text-slate-600 hover:text-sky-600 flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
            title="Chỉnh sửa sản phẩm"
          >
            <EditOutlined class="text-sm" />
          </button>

          <a-popconfirm
            :title="t('product.confirmDelete')"
            ok-text="Xóa"
            cancel-text="Hủy"
            ok-type="danger"
            placement="top"
            @confirm="() => handleDelete(product)"
          >
            <button
              type="button"
              class="w-8 h-8 rounded-lg bg-white border border-slate-200/90 hover:bg-rose-50 hover:border-rose-300 text-slate-400 hover:text-rose-600 flex items-center justify-center transition-colors cursor-pointer shadow-2xs"
              title="Xóa sản phẩm"
            >
              <DeleteOutlined class="text-sm" />
            </button>
          </a-popconfirm>
        </div>
      </div>
    </div>

    <!-- Pagination: Uniform across all screens -->
    <div
      v-if="pagination.total > 0"
      class="bg-white rounded-xl border border-slate-200/90 px-4 py-3 shadow-2xs flex flex-col sm:flex-row items-center justify-between gap-3"
    >
      <span class="text-xs sm:text-sm text-slate-500 text-center sm:text-left">
        {{ t('product.showingCount', { count: products.length, total: pagination.total }) }}
      </span>

      <a-pagination
        v-model:current="pagination.current"
        v-model:pageSize="pagination.pageSize"
        :total="pagination.total"
        :show-size-changer="!isMobile"
        :page-size-options="['20', '40', '60', '100']"
        size="small"
        class="flex justify-center sm:justify-end"
        @change="handlePageChange"
      />
    </div>

    <ProductFormModal
      v-model:visible="isModalVisible"
      :product="selectedProduct"
      @success="handleModalSuccess"
    />

    <ProductDetailModal
      v-model:visible="isDetailModalVisible"
      :product="selectedDetailProduct"
      @edit="openEditModal"
    />

    <a-modal
      v-model:open="isBarcodeModalVisible"
      :footer="null"
      :width="460"
      centered
      :title="null"
      :closable="false"
    >
      <div v-if="selectedBarcodeProduct" class="p-1 sm:p-3">

        <!-- Modal Header -->
        <div class="flex items-center justify-between pb-2.5 border-b border-slate-100">
          <div class="flex items-center gap-2 text-slate-800 font-bold text-sm">
            <BarcodeOutlined class="text-sky-600 text-base" />
            <span>{{ t('product.barcodeModalTitle') }}</span>
          </div>
          <button
            type="button"
            @click="isBarcodeModalVisible = false"
            class="w-7 h-7 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer text-lg leading-none"
          >
            &times;
          </button>
        </div>

        <!-- Product Summary Box -->
        <div class="my-2.5 bg-slate-50 p-2.5 sm:p-3 rounded-xl border border-slate-100 space-y-1">
          <h4 class="text-sm font-bold text-slate-800 line-clamp-2">
            {{ selectedBarcodeProduct.name }}
          </h4>
          <div class="flex items-center justify-between text-xs text-slate-500">
            <span>{{ t('product.codeSku', { code: selectedBarcodeProduct.code }) }}</span>
            <span>{{ t('product.baseUnitTag') }}: <span class="font-semibold text-slate-700">{{ selectedBarcodeProduct.unit?.name || '---' }}</span></span>
          </div>
        </div>

        <!-- Barcode Display Container -->
        <div class="bg-white p-3.5 sm:p-5 rounded-xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center">

          <div class="mb-2.5 flex items-center justify-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200/80 shadow-2xs">
              <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
              {{ t('product.barcodeDisplaying', { name: activeBarcodeItem?.name || '' }) }}
            </span>
          </div>

          <div class="w-full max-w-[280px] h-20 flex items-stretch justify-center py-1 px-4 bg-white select-none">

            <svg class="w-full h-full" viewBox="0 0 200 60" preserveAspectRatio="none">
              <rect x="0" y="0" width="4" height="60" fill="#1e293b"/>
              <rect x="6" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="11" y="0" width="6" height="60" fill="#1e293b"/>
              <rect x="19" y="0" width="3" height="60" fill="#1e293b"/>
              <rect x="25" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="30" y="0" width="5" height="60" fill="#1e293b"/>
              <rect x="38" y="0" width="4" height="60" fill="#1e293b"/>
              <rect x="45" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="50" y="0" width="7" height="60" fill="#1e293b"/>
              <rect x="60" y="0" width="3" height="60" fill="#1e293b"/>
              <rect x="66" y="0" width="5" height="60" fill="#1e293b"/>
              <rect x="74" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="78" y="0" width="6" height="60" fill="#1e293b"/>
              <rect x="87" y="0" width="3" height="60" fill="#1e293b"/>
              <rect x="93" y="0" width="5" height="60" fill="#1e293b"/>
              <rect x="101" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="106" y="0" width="6" height="60" fill="#1e293b"/>
              <rect x="115" y="0" width="4" height="60" fill="#1e293b"/>
              <rect x="122" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="127" y="0" width="5" height="60" fill="#1e293b"/>
              <rect x="135" y="0" width="3" height="60" fill="#1e293b"/>
              <rect x="141" y="0" width="6" height="60" fill="#1e293b"/>
              <rect x="150" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="155" y="0" width="5" height="60" fill="#1e293b"/>
              <rect x="163" y="0" width="3" height="60" fill="#1e293b"/>
              <rect x="169" y="0" width="6" height="60" fill="#1e293b"/>
              <rect x="178" y="0" width="2" height="60" fill="#1e293b"/>
              <rect x="183" y="0" width="5" height="60" fill="#1e293b"/>
              <rect x="191" y="0" width="3" height="60" fill="#1e293b"/>
              <rect x="196" y="0" width="4" height="60" fill="#1e293b"/>
            </svg>
          </div>

          <div class="mt-3 flex items-center justify-center gap-2">
            <span class="font-mono text-base font-extrabold tracking-wider text-slate-800">
              {{ activeBarcodeItem?.barcode || t('product.barcodeNotSet') }}
            </span>
            <button
              v-if="activeBarcodeItem?.barcode"
              type="button"
              @click="copyToClipboard(activeBarcodeItem.barcode)"
              class="px-2.5 py-1 bg-slate-100 hover:bg-sky-50 text-slate-600 hover:text-sky-600 rounded-md text-xs font-semibold flex items-center gap-1 transition-colors cursor-pointer"
              :title="t('product.copyBarcodeTitle')"
            >
              <CopyOutlined />
              <span>Copy</span>
            </button>
          </div>
        </div>

        <div
          v-if="conversionUnitsWithBarcodes.length > 0"
          class="mt-4 text-left"
        >
          <div class="flex items-center justify-between mb-1.5">
            <span class="text-xs font-semibold text-slate-600">{{ t('product.selectUnitToScan') }}</span>
            <span class="text-[11px] text-slate-400">{{ t('product.clickToSwitchBarcode') }}</span>
          </div>

          <div class="space-y-1.5 max-h-48 overflow-y-auto">

            <div
              @click="selectBarcodeUnit(selectedBarcodeProduct.unit?.name ? `${selectedBarcodeProduct.unit.name} (${t('product.baseUnitTag')})` : t('product.baseUnitTag'), selectedBarcodeProduct.barcode || '', 1)"
              class="flex items-center justify-between p-2.5 rounded-xl border transition-all cursor-pointer"
              :class="[
                activeBarcodeItem?.barcode === selectedBarcodeProduct.barcode
                  ? 'bg-sky-50/70 border-sky-400 shadow-2xs ring-1 ring-sky-300'
                  : 'bg-slate-50 hover:bg-slate-100/80 border-slate-200/80'
              ]"
            >
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :class="activeBarcodeItem?.barcode === selectedBarcodeProduct.barcode ? 'bg-sky-600' : 'bg-slate-300'"></span>
                <span class="font-bold text-xs text-slate-800">
                  {{ selectedBarcodeProduct.unit?.name || t('product.baseUnitTag') }}
                  <span class="font-normal text-slate-400 text-[11px] ml-1">({{ t('product.baseUnitTag') }})</span>
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-mono font-bold text-xs text-slate-800">{{ selectedBarcodeProduct.barcode }}</span>
                <button
                  type="button"
                  @click.stop="copyToClipboard(selectedBarcodeProduct.barcode)"
                  class="text-slate-400 hover:text-sky-600 transition-colors cursor-pointer p-0.5"
                  :title="t('product.copyBarcodeTitle')"
                >
                  <CopyOutlined />
                </button>
              </div>
            </div>

            <div
              v-for="(pu, pIndex) in conversionUnitsWithBarcodes"
              :key="pu.id ?? pIndex"
              @click="selectBarcodeUnit(`${pu.unit?.name || 'ĐVT'} (x${pu.conversion_rate})`, pu.barcode || '', pu.conversion_rate)"
              class="flex items-center justify-between p-2.5 rounded-xl border transition-all cursor-pointer"
              :class="[
                activeBarcodeItem?.barcode === pu.barcode
                  ? 'bg-sky-50/70 border-sky-400 shadow-2xs ring-1 ring-sky-300'
                  : 'bg-slate-50 hover:bg-slate-100/80 border-slate-200/80'
              ]"
            >
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full" :class="activeBarcodeItem?.barcode === pu.barcode ? 'bg-sky-600' : 'bg-slate-300'"></span>
                <span class="font-bold text-xs text-slate-800">
                  {{ pu.unit?.name || 'ĐVT' }}
                  <span class="font-normal text-slate-500 text-[11px] ml-1">(x{{ pu.conversion_rate }})</span>
                </span>
              </div>
              <div class="flex items-center gap-2">
                <span class="font-mono font-bold text-xs text-slate-800">{{ pu.barcode }}</span>
                <button
                  type="button"
                  @click.stop="copyToClipboard(pu.barcode)"
                  class="text-slate-400 hover:text-sky-600 transition-colors cursor-pointer p-0.5"
                  :title="t('product.copyBarcodeTitle')"
                >
                  <CopyOutlined />
                </button>
              </div>
            </div>
          </div>
        </div>

        <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-end">
          <button
            type="button"
            @click="isBarcodeModalVisible = false"
            class="px-4 h-8 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors cursor-pointer"
          >
            {{ t('product.closeModal') }}
          </button>
        </div>
      </div>
    </a-modal>
  </div>
</template>
