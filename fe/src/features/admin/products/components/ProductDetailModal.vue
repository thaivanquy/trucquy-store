<script setup lang="ts">
import { computed } from 'vue'
import {
  BarcodeOutlined,
  PictureOutlined,
  CheckCircleOutlined,
  WarningOutlined,
  CloseCircleOutlined,
  CopyOutlined,
  AppstoreOutlined,
  ShopOutlined,
} from '@ant-design/icons-vue'
import type { Product } from '../types'
import { formatPrice, calculateMargin, copyToClipboard } from '@/utils/format'
import { useI18n } from '@/i18n'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'

const { t } = useI18n()
const breakpoints = useBreakpoints(breakpointsTailwind)
const drawerWidth = computed(() => {
  if (breakpoints.smaller('md').value) return '100%'
  if (breakpoints.smaller('lg').value) return '80%'
  return 640
})

interface Props {
  visible: boolean
  product?: Product | null
}

const props = withDefaults(defineProps<Props>(), {
  product: null,
})

interface Emits {
  (e: 'update:visible', val: boolean): void
  (e: 'edit', product: Product): void
}

const emit = defineEmits<Emits>()

function handleClose() {
  emit('update:visible', false)
}

function handleEdit() {
  if (props.product) {
    emit('edit', props.product)
    handleClose()
  }
}

function handleCopyBarcode(code?: string | null) {
  if (code) {
    copyToClipboard(code, t('product.copiedBarcode', { code }))
  }
}

const conversionUnits = computed(() => {
  if (!props.product?.product_units) return []
  return props.product.product_units.filter((u) => !u.is_base_unit)
})

const defaultSupplier = computed(() => {
  if (props.product?.supplier) return props.product.supplier
  const defaultItem = props.product?.product_suppliers?.find((s) => s.is_default)
  return defaultItem?.supplier || null
})
</script>

<template>
  <a-drawer
    :open="visible"
    placement="right"
    :width="drawerWidth"
    :closable="false"
    :mask-closable="true"
    :z-index="1050"
    :body-style="{ padding: 0, height: '100%', display: 'flex', flexDirection: 'column' }"
    :header-style="{ display: 'none' }"
    :footer-style="{ display: 'none' }"
    class="custom-product-detail-drawer"
    @close="handleClose"
  >
    <div v-if="product" class="bg-white flex flex-col h-full text-slate-800">

      <!-- Drawer Header -->
      <div class="flex items-center justify-between px-2 sm:px-6 py-3.5 sm:py-4 border-b border-slate-100 shrink-0 bg-white">
        <h3 class="text-base font-bold text-slate-900 leading-tight">
          {{ t('product.modalDetailTitle') }}
        </h3>
        <button
          type="button"
          @click="handleClose"
          class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer text-lg leading-none"
        >
          &times;
        </button>
      </div>

      <!-- Drawer Content -->
      <div class="flex-1 px-2 py-3.5 sm:p-6 overflow-y-auto space-y-3.5 sm:space-y-5 bg-slate-50/30">

        <!-- Product Summary Header Card -->
        <div class="bg-white p-3.5 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3 sm:space-y-4">
          <!-- Primary Info: Image side-by-side with Title, Category & Price on both Mobile & Desktop -->
          <div class="flex items-start gap-3 sm:gap-4">
            <!-- Thumbnail -->
            <div class="w-24 h-24 sm:w-32 sm:h-32 rounded-xl sm:rounded-2xl bg-slate-50 border border-slate-200/90 overflow-hidden shrink-0 shadow-2xs flex items-center justify-center">
              <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="text-slate-300 flex flex-col items-center">
                <PictureOutlined class="text-2xl sm:text-3xl mb-1 opacity-60" />
                <span class="text-[11px] sm:text-xs text-slate-400">{{ t('product.noImage') }}</span>
              </div>
            </div>

            <!-- Title, Category & Price -->
            <div class="flex-1 min-w-0 flex flex-col justify-between self-stretch">
              <div>
                <!-- Category Eyebrow -->
                <div v-if="product.category?.name" class="mb-1">
                  <span class="inline-flex items-center gap-1 text-[11px] sm:text-xs font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md max-w-full truncate">
                    <AppstoreOutlined class="text-[10px] text-slate-400 shrink-0" />
                    <span class="truncate">{{ product.category.name }}</span>
                  </span>
                </div>

                <!-- Product Title -->
                <h4 class="text-sm sm:text-base font-bold text-slate-900 leading-snug line-clamp-2" :title="product.name">
                  {{ product.name }}
                </h4>
              </div>

              <!-- Selling Price -->
              <div class="pt-1.5 flex items-baseline gap-1.5 flex-wrap">
                <span class="text-lg sm:text-2xl font-black text-sky-600 sm:text-slate-900 tracking-tight">
                  {{ formatPrice(product.selling_price) }}
                </span>
                <span class="text-xs text-slate-500 font-medium">
                  / {{ product.unit?.name || 'ĐVT' }}
                </span>
              </div>
            </div>
          </div>

          <!-- Bottom: Clean, structured metadata strip for SKU & Barcode -->
          <div class="pt-2.5 border-t border-slate-100 flex flex-wrap items-center justify-between gap-2 text-xs">
            <!-- SKU -->
            <div class="flex items-center gap-1.5">
              <span class="text-slate-400 font-medium shrink-0">Mã SKU:</span>
              <span class="font-mono font-bold text-sky-700 bg-sky-50 border border-sky-200/80 px-2 py-0.5 rounded text-xs">
                {{ product.code }}
              </span>
            </div>

            <!-- Barcode with copy button -->
            <div v-if="product.barcode" class="flex items-center gap-1.5">
              <span class="text-slate-400 font-medium shrink-0">{{ t('product.colBarcode') }}:</span>
              <button
                type="button"
                @click="handleCopyBarcode(product.barcode)"
                class="inline-flex items-center gap-1 font-mono font-semibold text-slate-700 bg-slate-50 hover:bg-sky-50 hover:text-sky-700 hover:border-sky-300 border border-slate-200 px-2 py-0.5 rounded text-xs transition-colors cursor-pointer shadow-2xs"
                :title="t('product.copyBarcodeTitle')"
              >
                <BarcodeOutlined class="text-slate-400 text-xs shrink-0" />
                <span>{{ product.barcode }}</span>
                <CopyOutlined class="text-[10px] text-slate-400 ml-0.5 shrink-0" />
              </button>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5 sm:gap-3">

          <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-400 block">{{ t('product.costPriceLabel') }}</span>
            <span class="text-base font-bold text-slate-800 block mt-1">
              {{ formatPrice(product.cost_price) }}
            </span>
            <div class="mt-1">
              <span
                v-if="calculateMargin(product.selling_price, product.cost_price) !== null"
                class="text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded inline-block"
              >
                {{ t('product.profitMargin', { margin: calculateMargin(product.selling_price, product.cost_price) ?? 0 }) }}
              </span>
              <span v-else class="text-[10px] text-slate-400">{{ t('product.noCostPrice') }}</span>
            </div>
          </div>

          <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-400 block">{{ t('product.currentStock') }}</span>
            <div class="mt-1 flex items-baseline gap-1">
              <span
                class="text-base font-bold"
                :class="[
                  product.stock_quantity <= 0
                    ? 'text-rose-600'
                    : product.stock_quantity <= product.min_stock
                      ? 'text-amber-600'
                      : 'text-emerald-600'
                ]"
              >
                {{ product.stock_quantity }}
              </span>
              <span class="text-xs text-slate-500 font-medium">{{ product.unit?.name || '' }}</span>
            </div>
            <div class="mt-1">
              <span
                v-if="product.stock_quantity <= 0"
                class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded"
              >
                <CloseCircleOutlined /> {{ t('product.outOfStock') }}
              </span>
              <span
                v-else-if="product.stock_quantity <= product.min_stock"
                class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-600 bg-amber-50 px-1.5 py-0.5 rounded"
              >
                <WarningOutlined /> {{ t('product.needRestock') }}
              </span>
              <span
                v-else
                class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded"
              >
                <CheckCircleOutlined /> {{ t('product.inStock') }}
              </span>
            </div>
          </div>

          <div class="col-span-2 sm:col-span-1 bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-400 block">{{ t('product.minStockThreshold') }}</span>
            <div class="mt-1 flex items-baseline gap-1">
              <span class="text-base font-bold text-slate-800">
                {{ product.min_stock }}
              </span>
              <span class="text-xs text-slate-500 font-medium">{{ product.unit?.name || '' }}</span>
            </div>
            <span class="text-[10px] text-slate-400 block mt-1">{{ t('product.minStockWarning') }}</span>
          </div>
        </div>

        <!-- Conversion Units Table -->
        <div v-if="conversionUnits.length > 0" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
          <div class="flex items-center justify-between gap-2 flex-wrap">
            <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
              {{ t('product.conversionsSectionTitle', { count: conversionUnits.length }) }}
            </h5>
            <span class="text-xs text-slate-400">{{ t('product.conversionsSectionSubtitle') }}</span>
          </div>

          <div class="border border-slate-200/90 rounded-xl overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[360px]">
              <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200/80">
                <tr>
                  <th class="py-2.5 px-3 whitespace-nowrap">{{ t('product.colSpecName') }}</th>
                  <th class="py-2.5 px-3 whitespace-nowrap">{{ t('product.colRate') }}</th>
                  <th class="py-2.5 px-3 whitespace-nowrap">{{ t('product.colBarcode') }}</th>
                  <th class="py-2.5 px-3 text-right whitespace-nowrap">{{ t('product.colPrice') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                <tr v-for="(cu, index) in conversionUnits" :key="cu.id ?? index" class="hover:bg-slate-50/50">
                  <td class="py-2.5 px-3 font-bold text-slate-800 whitespace-nowrap">
                    {{ cu.unit?.name || 'ĐVT quy đổi' }}
                  </td>
                  <td class="py-2.5 px-3 text-slate-500 whitespace-nowrap">
                    x{{ cu.conversion_rate }} {{ product.unit?.name || 'ĐVT' }}
                  </td>
                  <td class="py-2.5 px-3 font-mono text-slate-600 whitespace-nowrap">
                    <span
                      v-if="cu.barcode"
                      @click="handleCopyBarcode(cu.barcode)"
                      class="inline-flex items-center gap-1 bg-slate-100 hover:bg-sky-50 px-2 py-0.5 rounded text-xs cursor-pointer border border-slate-200/60"
                      :title="t('product.copyBarcodeTitle')"
                    >
                      {{ cu.barcode }}
                      <CopyOutlined class="text-[10px] text-slate-400" />
                    </span>
                    <span v-else class="text-slate-300">---</span>
                  </td>
                  <td class="py-2.5 px-3 text-right font-bold text-sky-700 whitespace-nowrap">
                    {{ formatPrice(cu.selling_price) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Supplier Info -->
        <div v-if="defaultSupplier?.name" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-2">
          <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
            <ShopOutlined class="text-sky-600" />
            <span>{{ t('product.supplierSectionTitle') }}</span>
          </h5>
          <div class="text-xs bg-slate-50 p-2.5 sm:p-3 rounded-xl border border-slate-100 flex flex-wrap sm:flex-nowrap items-center justify-between gap-2">
            <span class="font-bold text-slate-800 leading-snug">{{ defaultSupplier.name }}</span>
            <span v-if="defaultSupplier.code" class="text-slate-600 font-mono font-medium shrink-0 whitespace-nowrap bg-white px-2 py-0.5 rounded-md border border-slate-200/80 text-[11px]">
              {{ defaultSupplier.code }}
            </span>
          </div>
        </div>

        <!-- Description -->
        <div v-if="product.description" class="bg-white p-3.5 sm:p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-1.5">
          <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
            {{ t('product.descSectionTitle') }}
          </h5>
          <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed whitespace-pre-line">
            {{ product.description }}
          </p>
        </div>
      </div>

      <!-- Drawer Footer -->
      <div class="flex items-center justify-end gap-2 px-2 sm:px-6 py-3 border-t border-slate-100 bg-white shrink-0 pb-safe">
        <button
          type="button"
          @click="handleClose"
          class="px-4 h-9 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs sm:text-sm font-semibold transition-colors cursor-pointer"
        >
          {{ t('product.closeModal') }}
        </button>

        <button
          type="button"
          @click="handleEdit"
          class="inline-flex items-center justify-center px-4.5 h-9 rounded-xl bg-sky-600 hover:bg-sky-700 !text-white text-xs sm:text-sm font-semibold shadow-xs transition-colors cursor-pointer"
        >
          <span class="!text-white">{{ t('product.editProductBtn') }}</span>
        </button>
      </div>
    </div>
  </a-drawer>
</template>
