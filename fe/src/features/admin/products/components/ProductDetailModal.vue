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

const { t } = useI18n()

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
    :width="640"
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

      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0 bg-white">
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

      <div class="flex-1 p-6 overflow-y-auto space-y-5 bg-slate-50/30">

        <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-4">
          <div class="flex flex-col sm:flex-row items-start gap-4">

            <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl bg-slate-50 border border-slate-200/90 overflow-hidden shrink-0 shadow-2xs flex items-center justify-center">
              <img
                v-if="product.image"
                :src="product.image"
                :alt="product.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="text-slate-300 flex flex-col items-center">
                <PictureOutlined class="text-2xl mb-1 opacity-60" />
                <span class="text-[10px] text-slate-400">{{ t('product.noImage') }}</span>
              </div>
            </div>

            <div class="flex-1 space-y-2">
              <h4 class="text-base font-bold text-slate-900 leading-snug">
                {{ product.name }}
              </h4>

              <div class="flex flex-wrap items-center gap-2">

                <span class="inline-flex items-center font-mono text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200 px-2.5 py-0.5 rounded-md">
                  {{ t('product.codeSku', { code: product.code }) }}
                </span>

                <button
                  v-if="product.barcode"
                  type="button"
                  @click="handleCopyBarcode(product.barcode)"
                  class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-sky-50 hover:text-sky-700 hover:border-sky-300 border border-slate-200 px-2 py-0.5 rounded-md transition-colors cursor-pointer"
                  :title="t('product.copyBarcodeTitle')"
                >
                  <BarcodeOutlined class="text-slate-400 text-xs" />
                  <span>{{ product.barcode }}</span>
                  <CopyOutlined class="text-[10px] text-slate-400 ml-0.5" />
                </button>

                <span
                  v-if="product.category?.name"
                  class="inline-flex items-center gap-1 text-xs font-medium text-slate-600 bg-slate-100/80 px-2 py-0.5 rounded-md border border-slate-200/80"
                >
                  <AppstoreOutlined class="text-[11px] text-slate-400" />
                  {{ product.category.name }}
                </span>
              </div>

              <div class="pt-2 flex items-baseline gap-2">
                <span class="text-2xl font-extrabold text-slate-900 tracking-tight">
                  {{ formatPrice(product.selling_price) }}
                </span>
                <span class="text-xs text-slate-500 font-medium">
                  / {{ product.unit?.name || 'ĐVT' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

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

          <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
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

        <div v-if="conversionUnits.length > 0" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
          <div class="flex items-center justify-between">
            <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
              {{ t('product.conversionsSectionTitle', { count: conversionUnits.length }) }}
            </h5>
            <span class="text-[11px] text-slate-400">{{ t('product.conversionsSectionSubtitle') }}</span>
          </div>

          <div class="border border-slate-200/90 rounded-xl overflow-hidden">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200/80">
                <tr>
                  <th class="py-2 px-3">{{ t('product.colSpecName') }}</th>
                  <th class="py-2 px-3">{{ t('product.colRate') }}</th>
                  <th class="py-2 px-3">{{ t('product.colBarcode') }}</th>
                  <th class="py-2 px-3 text-right">{{ t('product.colPrice') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                <tr v-for="(cu, index) in conversionUnits" :key="cu.id ?? index" class="hover:bg-slate-50/50">
                  <td class="py-2 px-3 font-bold text-slate-800">
                    {{ cu.unit?.name || 'ĐVT quy đổi' }}
                  </td>
                  <td class="py-2 px-3 text-slate-500">
                    x{{ cu.conversion_rate }} {{ product.unit?.name || 'ĐVT' }}
                  </td>
                  <td class="py-2 px-3 font-mono text-slate-600">
                    <span
                      v-if="cu.barcode"
                      @click="handleCopyBarcode(cu.barcode)"
                      class="inline-flex items-center gap-1 bg-slate-100 hover:bg-sky-50 px-1.5 py-0.5 rounded text-[11px] cursor-pointer"
                      :title="t('product.copyBarcodeTitle')"
                    >
                      {{ cu.barcode }}
                      <CopyOutlined class="text-[10px] text-slate-400" />
                    </span>
                    <span v-else class="text-slate-300">---</span>
                  </td>
                  <td class="py-2 px-3 text-right font-bold text-sky-700">
                    {{ formatPrice(cu.selling_price) }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <div v-if="defaultSupplier?.name" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-2">
          <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
            <ShopOutlined class="text-sky-600" />
            <span>{{ t('product.supplierSectionTitle') }}</span>
          </h5>
          <div class="text-xs bg-slate-50 p-2.5 rounded-xl border border-slate-100 flex items-center gap-2">
            <span class="font-bold text-slate-800">{{ defaultSupplier.name }}</span>
            <span v-if="defaultSupplier.code" class="text-slate-400 font-mono">({{ defaultSupplier.code }})</span>
          </div>
        </div>

        <div v-if="product.description" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-1.5">
          <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
            {{ t('product.descSectionTitle') }}
          </h5>
          <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed whitespace-pre-line">
            {{ product.description }}
          </p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-slate-100 bg-white shrink-0">
        <button
          type="button"
          @click="handleClose"
          class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer"
        >
          {{ t('product.closeModal') }}
        </button>

        <button
          type="button"
          @click="handleEdit"
          class="inline-flex items-center justify-center px-4.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 !text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer"
        >
          <span class="!text-white">{{ t('product.editProductBtn') }}</span>
        </button>
      </div>
    </div>
  </a-drawer>
</template>
