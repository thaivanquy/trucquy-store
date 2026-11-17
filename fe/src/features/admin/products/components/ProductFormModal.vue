<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import {
  BarcodeOutlined,
  DollarOutlined,
  PlusOutlined,
  DeleteOutlined,
  AppstoreOutlined,
  ShopOutlined,
  InboxOutlined,
  InfoCircleOutlined,
} from '@ant-design/icons-vue'
import type { Product, ProductFormData, ProductUnitItem } from '../types'
import { useProductStore } from '../stores/productStore'
import { useCategoryStore } from '@/features/admin/categories/stores/categoryStore'
import { useUnitStore } from '@/features/admin/units/stores/unitStore'
import { useSupplierStore } from '@/features/admin/suppliers/stores/supplierStore'
import AppImageUpload from '@/components/common/AppImageUpload.vue'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'
import { generateBarcode } from '@/utils/generator'
import { priceInputFormatter, priceInputParser } from '@/utils/format'

interface Props {
  visible: boolean
  product?: Product | null
}

const props = withDefaults(defineProps<Props>(), {
  product: null,
})

interface Emits {
  (e: 'update:visible', val: boolean): void
  (e: 'success'): void
}

const emit = defineEmits<Emits>()

const toast = useToast()
const { t } = useI18n()

const productStore = useProductStore()
const categoryStore = useCategoryStore()
const unitStore = useUnitStore()
const supplierStore = useSupplierStore()

const isSubmitting = ref(false)

const activeTab = ref<'general' | 'units' | 'supplier'>('general')

const form = ref<{
  id?: number
  code: string
  name: string
  category_id: number | null
  image: string
  description: string
  is_active: boolean

  base_unit_id: number | null
  base_barcode: string
  cost_price: number
  selling_price: number
  stock_quantity: number
  min_stock: number

  conversion_units: Array<{
    id?: number
    unit_id: number | null
    barcode: string
    conversion_rate: number
    cost_price: number
    selling_price: number
  }>

  supplier_id: number | null
  supplier_product_code: string
  purchase_price: number
}>({
  code: '',
  name: '',
  category_id: null,
  image: '',
  description: '',
  is_active: true,

  base_unit_id: null,
  base_barcode: '',
  cost_price: 0,
  selling_price: 0,
  stock_quantity: 0,
  min_stock: 5,

  conversion_units: [],

  supplier_id: null,
  supplier_product_code: '',
  purchase_price: 0,
})

const errors = reactive<Record<string, string>>({
  name: '',
  code: '',
  base_unit_id: '',
  selling_price: '',
  cost_price: '',
  stock_quantity: '',
  min_stock: '',
})

function resetErrors() {
  Object.keys(errors).forEach((key) => {
    errors[key] = ''
  })
}

function clearError(field: string) {
  if (errors[field]) {
    errors[field] = ''
  }
}

const isEdit = computed(() => !!props.product?.id)

const baseUnitName = computed(() => {
  if (!form.value.base_unit_id) return 'Đơn vị cơ bản'
  const u = unitStore.allUnits.find((item) => item.id === form.value.base_unit_id)
  return u ? u.name : 'Đơn vị cơ bản'
})

function addConversionUnit() {
  const defaultRate = 10
  form.value.conversion_units.push({
    unit_id: null,
    barcode: '',
    conversion_rate: defaultRate,
    cost_price: Math.round(form.value.cost_price * defaultRate),
    selling_price: Math.round(form.value.selling_price * defaultRate),
  })
}

function removeConversionUnit(index: number) {
  form.value.conversion_units.splice(index, 1)
}

function handleConversionRateChange(index: number) {
  const item = form.value.conversion_units[index]
  if (item && item.conversion_rate > 0) {
    if (form.value.cost_price > 0 && (!item.cost_price || item.cost_price === 0)) {
      item.cost_price = Math.round(form.value.cost_price * item.conversion_rate)
    }
    if (form.value.selling_price > 0 && (!item.selling_price || item.selling_price === 0)) {
      item.selling_price = Math.round(form.value.selling_price * item.conversion_rate)
    }
  }
}

function handleGenerateBaseBarcode() {
  form.value.base_barcode = generateBarcode('200')
  toast.success('Đã tự động tạo mã vạch nội bộ: ' + form.value.base_barcode)
}

function handleGenerateUnitBarcode(index: number) {
  const cu = form.value.conversion_units[index]
  if (cu) {
    cu.barcode = generateBarcode('201')
    toast.success('Đã tự động tạo mã vạch: ' + cu.barcode)
  }
}

watch(
  () => props.visible,
  async (val) => {
    resetErrors()
    activeTab.value = 'general'
    if (val) {
      if (categoryStore.allCategories.length === 0) {
        categoryStore.fetchDropdownCategories()
      }
      if (unitStore.allUnits.length === 0) {
        unitStore.fetchAllUnits()
      }
      if (supplierStore.suppliers.length === 0) {
        supplierStore.fetchSuppliers({ all: true })
      }

      if (props.product) {
        const p = props.product

        const pUnits = p.product_units || []
        const baseU = pUnits.find((u) => u.is_base_unit) || pUnits[0]
        const convUnits = pUnits.filter((u) => !u.is_base_unit && u !== baseU)

        const pSuppliers = p.product_suppliers || []
        const defaultSup = pSuppliers.find((s) => s.is_default) || pSuppliers[0]

        form.value = {
          id: p.id,
          code: p.code,
          name: p.name,
          category_id: p.category_id ?? null,
          image: p.image || '',
          description: p.description || '',
          is_active: Boolean(p.is_active),

          base_unit_id: baseU ? baseU.unit_id : (p.unit_id ?? null),
          base_barcode: baseU?.barcode || p.barcode || '',
          cost_price: Number(baseU?.cost_price ?? p.cost_price ?? p.inventory?.cost_price ?? 0),
          selling_price: Number(baseU?.selling_price ?? p.selling_price ?? 0),
          stock_quantity: Number(p.stock_quantity ?? p.inventory?.quantity ?? 0),
          min_stock: Number(p.min_stock ?? p.inventory?.min_stock ?? 5),

          conversion_units: convUnits.map((u) => ({
            id: u.id,
            unit_id: u.unit_id,
            barcode: u.barcode || '',
            conversion_rate: Number(u.conversion_rate) || 1,
            cost_price: Number(u.cost_price) || 0,
            selling_price: Number(u.selling_price) || 0,
          })),

          supplier_id: defaultSup ? defaultSup.supplier_id : (p.supplier_id ?? null),
          supplier_product_code: defaultSup?.supplier_product_code || '',
          purchase_price: Number(defaultSup?.purchase_price ?? baseU?.cost_price ?? p.cost_price ?? 0),
        }
      } else {
        form.value = {
          code: '',
          name: '',
          category_id: null,
          image: '',
          description: '',
          is_active: true,

          base_unit_id: null,
          base_barcode: '',
          cost_price: 0,
          selling_price: 0,
          stock_quantity: 0,
          min_stock: 5,

          conversion_units: [],

          supplier_id: null,
          supplier_product_code: '',
          purchase_price: 0,
        }
      }
    }
  },
  { immediate: true }
)

function handleCancel() {
  emit('update:visible', false)
}

async function handleSubmit() {
  resetErrors()

  let hasError = false
  if (!form.value.name || !form.value.name.trim()) {
    errors.name = 'Vui lòng nhập tên sản phẩm'
    hasError = true
  }

  if (!form.value.base_unit_id) {
    errors.base_unit_id = 'Vui lòng chọn đơn vị tính cơ bản'
    hasError = true
  }

  if (form.value.selling_price < 0) {
    errors.selling_price = 'Giá bán không được nhỏ hơn 0'
    hasError = true
  }

  if (hasError) {
    if (errors.name) {
      activeTab.value = 'general'
    } else if (errors.base_unit_id || errors.selling_price) {
      activeTab.value = 'units'
    }
    return
  }

  const unitsPayload: ProductUnitItem[] = [
    {
      unit_id: form.value.base_unit_id,
      barcode: form.value.base_barcode?.trim() || null,
      conversion_rate: 1,
      is_base_unit: true,
      cost_price: Number(form.value.cost_price) || 0,
      selling_price: Number(form.value.selling_price) || 0,
      is_direct_sale: true,
    },
  ]

  form.value.conversion_units.forEach((cu) => {
    if (cu.unit_id) {
      unitsPayload.push({
        id: cu.id,
        unit_id: cu.unit_id,
        barcode: cu.barcode?.trim() || null,
        conversion_rate: Number(cu.conversion_rate) || 1,
        is_base_unit: false,
        cost_price: Number(cu.cost_price) || 0,
        selling_price: Number(cu.selling_price) || 0,
        is_direct_sale: true,
      })
    }
  })

  const suppliersPayload = form.value.supplier_id
    ? [
        {
          supplier_id: form.value.supplier_id,
          supplier_product_code: form.value.supplier_product_code?.trim() || null,
          purchase_price: Number(form.value.purchase_price) || Number(form.value.cost_price) || 0,
          is_default: true,
        },
      ]
    : []

  const payload: ProductFormData = {
    code: isEdit.value ? form.value.code?.trim() || undefined : undefined,
    name: form.value.name.trim(),
    category_id: form.value.category_id ? Number(form.value.category_id) : null,
    image: form.value.image || null,
    description: form.value.description?.trim() || null,
    is_active: form.value.is_active ?? true,

    cost_price: Number(form.value.cost_price) || 0,
    selling_price: Number(form.value.selling_price) || 0,
    stock_quantity: Number(form.value.stock_quantity) || 0,
    min_stock: Number(form.value.min_stock) || 0,

    units: unitsPayload,
    suppliers: suppliersPayload,
  }

  isSubmitting.value = true
  try {
    if (isEdit.value && props.product?.id) {
      await productStore.updateProduct(props.product.id, payload)
      toast.success(t('product.updateSuccess', { name: payload.name }))
    } else {
      await productStore.createProduct(payload)
      toast.success(t('product.createSuccess', { name: payload.name }))
    }
    emit('update:visible', false)
    emit('success')
  } catch (err: any) {
    const apiErrors = err.response?.data?.errors
    if (apiErrors) {
      Object.keys(apiErrors).forEach((key) => {
        errors[key] = apiErrors[key][0]
      })
    }
    const message =
      err.response?.data?.message ||
      err.message ||
      t('product.saveError')
    toast.error(message)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <a-drawer
    :open="visible"
    placement="right"
    :width="760"
    :closable="false"
    :mask-closable="true"
    destroy-on-close
    :z-index="1050"
    :body-style="{ padding: 0, height: '100%', display: 'flex', flexDirection: 'column' }"
    :header-style="{ display: 'none' }"
    :footer-style="{ display: 'none' }"
    class="custom-product-drawer"
    @close="handleCancel"
  >
    <div class="bg-white flex flex-col h-full">

      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0">
        <h3 class="text-base font-bold text-slate-900 leading-tight">
          {{ isEdit ? t('product.modalEditTitle') : t('product.modalCreateTitle') }}
        </h3>
        <button
          type="button"
          @click="handleCancel"
          class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer"
        >
          <span class="text-lg leading-none">&times;</span>
        </button>
      </div>

      <div class="px-6 pt-2 border-b border-slate-100 flex items-center gap-6 bg-slate-50/60 shrink-0 text-xs font-semibold">
        <button
          type="button"
          @click="activeTab = 'general'"
          class="pb-2.5 pt-1.5 border-b-2 transition-all cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'general' ? 'border-sky-600 text-sky-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
        >
          <InfoCircleOutlined />
          <span>{{ t('product.tabGeneral') }}</span>
        </button>

        <button
          type="button"
          @click="activeTab = 'units'"
          class="pb-2.5 pt-1.5 border-b-2 transition-all cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'units' ? 'border-sky-600 text-sky-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
        >
          <DollarOutlined />
          <span>{{ t('product.tabUnits') }}</span>
          <span
            v-if="form.conversion_units.length > 0"
            class="px-1.5 py-0.2 rounded-full text-[10px] bg-sky-100 text-sky-700 font-bold"
          >
            +{{ form.conversion_units.length }}
          </span>
        </button>

        <button
          type="button"
          @click="activeTab = 'supplier'"
          class="pb-2.5 pt-1.5 border-b-2 transition-all cursor-pointer flex items-center gap-1.5"
          :class="activeTab === 'supplier' ? 'border-sky-600 text-sky-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-800'"
        >
          <ShopOutlined />
          <span>{{ t('product.tabSupplier') }}</span>
        </button>
      </div>

      <form
        @submit.prevent="handleSubmit"
        id="product-form"
        class="flex-1 overflow-y-auto px-6 py-4 space-y-4"
      >

        <div v-show="activeTab === 'general'" class="space-y-4">

          <div v-if="isEdit">
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              {{ t('product.codeLabel') }}
            </label>
            <a-input
              :value="form.code"
              readonly
              disabled
              size="middle"
              class="!rounded-lg font-mono uppercase !text-sm !bg-slate-100 !text-slate-500 cursor-not-allowed"
            >
              <template #prefix><BarcodeOutlined class="text-slate-400 text-sm mr-0.5" /></template>
            </a-input>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              {{ t('product.nameLabel') }} <span class="text-rose-500">*</span>
            </label>
            <a-input
              v-model:value="form.name"
              :placeholder="t('product.namePlaceholder')"
              size="middle"
              :status="errors.name ? 'error' : ''"
              class="!rounded-lg !text-sm"
              @input="clearError('name')"
            />
            <span v-if="errors.name" class="text-xs text-rose-500 block mt-1">
              {{ errors.name }}
            </span>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              {{ t('product.categoryLabel') }}
            </label>
            <a-select
              v-model:value="form.category_id"
              :placeholder="t('product.categoryPlaceholder')"
              class="w-full !rounded-lg !text-sm"
              allow-clear
              size="middle"
            >
              allow-clear
              size="middle"
            >
              <a-select-option
                v-for="cat in categoryStore.allCategories"
                :key="cat.id"
                :value="cat.id"
              >
                {{ cat.name }}
              </a-select-option>
            </a-select>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              {{ t('product.imageLabel') }}
            </label>
            <AppImageUpload
              v-model="form.image"
              :placeholder="t('product.imageUploadPlaceholder')"
              :help-text="t('product.imageHelpText')"
              preview-height="120px"
              class="w-full"
            />
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              {{ t('product.descSectionTitle') }}
            </label>
            <a-textarea
              v-model:value="form.description"
              :placeholder="t('product.descriptionPlaceholder')"
              :rows="3"
              class="!rounded-lg !text-sm"
            />
          </div>
        </div>

        <div v-show="activeTab === 'units'" class="space-y-5">

          <div class="bg-slate-50/80 rounded-2xl border border-slate-200 p-4 space-y-3.5">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                {{ t('product.sectionBasic') }}
              </span>
              <span class="text-[11px] text-slate-400">{{ t('product.baseUnitSubtitle') }}</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">

              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.baseUnitNameLabel') }} <span class="text-rose-500">*</span>
                </label>
                <a-select
                  v-model:value="form.base_unit_id"
                  :placeholder="t('product.baseUnitPlaceholder')"
                  class="w-full !rounded-lg !text-sm"
                  :status="errors.base_unit_id ? 'error' : ''"
                  size="middle"
                  @change="clearError('base_unit_id')"
                >
                  <a-select-option
                    v-for="u in unitStore.allUnits"
                    :key="u.id"
                    :value="u.id"
                  >
                    {{ u.name }}
                  </a-select-option>
                </a-select>
                <span v-if="errors.base_unit_id" class="text-xs text-rose-500 block mt-1">
                  {{ errors.base_unit_id }}
                </span>
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.baseBarcodeLabel') }}
                </label>
                <a-input
                  v-model:value="form.base_barcode"
                  :placeholder="t('product.baseBarcodePlaceholder')"
                  size="middle"
                  class="!rounded-lg !text-sm"
                  allow-clear
                >
                  <template #prefix><BarcodeOutlined class="text-slate-400 text-sm" /></template>
                  <template #suffix>
                    <a-tooltip :title="t('product.autoGenBarcodeTooltip')">
                      <button
                        type="button"
                        @click="handleGenerateBaseBarcode"
                        class="text-[11px] text-slate-500 hover:text-sky-600 px-1.5 py-0.5 rounded bg-slate-100 hover:bg-sky-50 transition-colors cursor-pointer font-medium"
                      >
                        {{ t('product.autoGenBarcode') }}
                      </button>
                    </a-tooltip>
                  </template>
                </a-input>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.costPriceVnd') }}
                </label>
                <a-input-number
                  v-model:value="form.cost_price"
                  :min="0"
                  :step="1000"
                  :formatter="priceInputFormatter"
                  :parser="priceInputParser"
                  class="!w-full !rounded-lg !text-sm"
                  size="middle"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.sellingPriceVnd') }} <span class="text-rose-500">*</span>
                </label>
                <a-input-number
                  v-model:value="form.selling_price"
                  :min="0"
                  :step="1000"
                  :status="errors.selling_price ? 'error' : ''"
                  :formatter="priceInputFormatter"
                  :parser="priceInputParser"
                  class="!w-full !rounded-lg !text-sm"
                  size="middle"
                  @input="clearError('selling_price')"
                />
                <span v-if="errors.selling_price" class="text-xs text-rose-500 block mt-1">
                  {{ errors.selling_price }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.initialStockByUnit', { unit: baseUnitName }) }}
                </label>
                <a-input-number
                  v-model:value="form.stock_quantity"
                  :min="0"
                  :step="1"
                  class="!w-full !rounded-lg !text-sm"
                  size="middle"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.minStockAlert') }}
                </label>
                <a-input-number
                  v-model:value="form.min_stock"
                  :min="0"
                  :step="1"
                  class="!w-full !rounded-lg !text-sm"
                  size="middle"
                />
              </div>
            </div>
          </div>

          <div class="bg-white rounded-2xl border border-slate-200 p-4 space-y-3">
            <div class="flex items-center justify-between">
              <span class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                {{ t('product.sectionConversions') }}
              </span>

              <button
                type="button"
                @click="addConversionUnit"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-sky-50 hover:bg-sky-100 text-sky-700 text-xs font-semibold transition-colors cursor-pointer"
              >
                <PlusOutlined class="text-xs" />
                <span>{{ t('product.addConversionBtn') }}</span>
              </button>
            </div>

            <div
              v-if="form.conversion_units.length === 0"
              class="py-5 text-center bg-slate-50/50 rounded-xl border border-dashed border-slate-200 text-xs text-slate-400"
            >
              {{ t('product.emptyConversions') }}
            </div>

            <div v-else class="space-y-3">
              <div
                v-for="(cu, idx) in form.conversion_units"
                :key="idx"
                class="bg-slate-50/60 rounded-xl border border-slate-200/80 p-3 space-y-2 relative"
              >

                <button
                  type="button"
                  @click="removeConversionUnit(idx)"
                  class="absolute top-2.5 right-2.5 w-6 h-6 rounded-lg text-slate-400 hover:text-rose-600 hover:bg-rose-50 flex items-center justify-center transition-colors cursor-pointer"
                  :title="t('product.deleteBtn')"
                >
                  <DeleteOutlined class="text-xs" />
                </button>

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5">

                  <div class="sm:col-span-4">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                      {{ t('product.conversionUnitLabel') }}
                    </label>
                    <a-select
                      v-model:value="cu.unit_id"
                      :placeholder="t('product.conversionUnitPlaceholder')"
                      class="w-full !rounded-lg !text-sm"
                      size="middle"
                    >
                      <a-select-option
                        v-for="u in unitStore.allUnits"
                        :key="u.id"
                        :value="u.id"
                        :disabled="u.id === form.base_unit_id"
                      >
                        {{ u.name }}
                      </a-select-option>
                    </a-select>
                  </div>

                  <div class="sm:col-span-3">
                    <label class="block text-xs font-semibold text-slate-700 mb-1 truncate" :title="`x${baseUnitName}`">
                      {{ t('product.conversionRateLabel', { unit: baseUnitName }) }}
                    </label>
                    <a-input-number
                      v-model:value="cu.conversion_rate"
                      :min="0.01"
                      :step="1"
                      class="!w-full !rounded-lg !text-sm"
                      size="middle"
                      @change="() => handleConversionRateChange(idx)"
                    />
                  </div>

                  <div class="sm:col-span-5">
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                      {{ t('product.conversionBarcodeLabel') }}
                    </label>
                    <a-input
                      v-model:value="cu.barcode"
                      :placeholder="t('product.conversionBarcodePlaceholder')"
                      size="middle"
                      class="!rounded-lg !text-sm"
                      allow-clear
                    >
                      <template #suffix>
                        <a-tooltip :title="t('product.autoGenBarcodeTooltip')">
                          <button
                            type="button"
                            @click="() => handleGenerateUnitBarcode(idx)"
                            class="text-[11px] text-slate-500 hover:text-sky-600 px-1.5 py-0.5 rounded bg-slate-100 hover:bg-sky-50 transition-colors cursor-pointer font-medium"
                          >
                            {{ t('product.autoGenBarcode') }}
                          </button>
                        </a-tooltip>
                      </template>
                    </a-input>
                  </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">

                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                      {{ t('product.conversionSellingPriceLabel') }}
                    </label>
                    <a-input-number
                      v-model:value="cu.selling_price"
                      :min="0"
                      :step="1000"
                      :formatter="priceInputFormatter"
                      :parser="priceInputParser"
                      class="!w-full !rounded-lg !text-sm"
                      size="middle"
                    />
                  </div>

                  <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1">
                      {{ t('product.conversionCostPriceLabel') }}
                    </label>
                    <a-input-number
                      v-model:value="cu.cost_price"
                      :min="0"
                      :step="1000"
                      :formatter="priceInputFormatter"
                      :parser="priceInputParser"
                      class="!w-full !rounded-lg !text-sm"
                      size="middle"
                    />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-show="activeTab === 'supplier'" class="space-y-4">
          <div class="bg-slate-50/80 rounded-2xl border border-slate-200 p-4 space-y-3.5">
            <div>
              <span class="text-xs font-bold text-slate-800 uppercase tracking-wide flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-violet-500"></span>
                {{ t('product.sectionSupplier') }}
              </span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('product.selectSupplierLabel') }}
              </label>
              <a-select
                v-model:value="form.supplier_id"
                :placeholder="t('product.selectSupplierPlaceholder')"
                class="w-full !rounded-lg !text-sm"
                allow-clear
                size="middle"
              >
                <a-select-option
                  v-for="sup in supplierStore.suppliers"
                  :key="sup.id"
                  :value="sup.id"
                >
                  {{ sup.name }} ({{ sup.code }})
                </a-select-option>
              </a-select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pt-1">
              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.supplierCodeLabel') }}
                </label>
                <a-input
                  v-model:value="form.supplier_product_code"
                  :placeholder="t('product.supplierCodePlaceholder')"
                  size="middle"
                  class="!rounded-lg !text-sm"
                />
              </div>

              <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">
                  {{ t('product.purchasePriceLabel') }}
                </label>
                <a-input-number
                  v-model:value="form.purchase_price"
                  :min="0"
                  :step="1000"
                  :formatter="priceInputFormatter"
                  :parser="priceInputParser"
                  class="!w-full !rounded-lg !text-sm"
                  size="middle"
                />
              </div>
            </div>
          </div>
        </div>
      </form>

      <div class="flex items-center justify-end gap-2.5 px-6 py-3.5 border-t border-slate-100 bg-slate-50/70 shrink-0">
        <button
          type="button"
          @click="handleCancel"
          class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-white text-xs font-semibold transition-colors cursor-pointer"
        >
          {{ t('product.cancelBtn') }}
        </button>

        <button
          type="button"
          @click="handleSubmit"
          :disabled="isSubmitting"
          class="inline-flex items-center gap-1.5 px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-xs font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50"
        >
          <span v-if="isSubmitting" class="!text-white">{{ t('product.savingBtn') }}</span>
          <span v-else class="!text-white">{{ isEdit ? t('product.updateSubmitBtn') : t('product.createSubmitBtn') }}</span>
        </button>
      </div>
    </div>
  </a-drawer>
</template>
