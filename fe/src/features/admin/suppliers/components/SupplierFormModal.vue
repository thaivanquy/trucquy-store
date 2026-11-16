<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import {
  TagOutlined,
  BarcodeOutlined,
  PhoneOutlined,
  MailOutlined,
  BankOutlined,
  UserOutlined,
  HomeOutlined,
} from '@ant-design/icons-vue'
import type { Bank, Province, Supplier, SupplierFormData, Ward } from '../types'
import { supplierSchema } from '../types'
import { useSupplierStore } from '../stores/supplierStore'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'

interface Props {
  visible: boolean
  supplier?: Supplier | null
  mode?: 'create' | 'edit' | 'view'
}

const props = withDefaults(defineProps<Props>(), {
  supplier: null,
  mode: 'create',
})

interface Emits {
  (e: 'update:visible', val: boolean): void
  (e: 'success'): void
}

const emit = defineEmits<Emits>()

const toast = useToast()
const { t } = useI18n()
const supplierStore = useSupplierStore()

const isSubmitting = ref(false)
const isLoadingData = ref(false)
const currentMode = ref<'create' | 'edit' | 'view'>('create')

const provinces = ref<Province[]>([])
const wards = ref<Ward[]>([])
const banks = ref<Bank[]>([])
const isLoadingWards = ref(false)

const defaultForm: SupplierFormData = {
  code: '',
  name: '',
  phone: '',
  email: '',
  address: '',
  province_id: undefined,
  ward_id: undefined,
  contact_person: '',
  contact_phone: '',
  bank_name: undefined,
  bank_account: '',
  bank_account_name: '',
  opening_balance: 0,
  note: '',
  is_active: true,
}

const form = ref<SupplierFormData>({ ...defaultForm })

const errors = reactive<Record<string, string>>({})

function resetErrors() {
  Object.keys(errors).forEach((key) => {
    delete errors[key]
  })
}

function clearError(field: string) {
  if (errors[field]) {
    delete errors[field]
  }
}

const isEdit = computed(() => currentMode.value === 'edit')
const isView = computed(() => currentMode.value === 'view')

const selectedProvince = computed(() => {
  if (!form.value.province_id) return null
  return provinces.value.find((p) => p.code === Number(form.value.province_id)) || null
})

const selectedWard = computed(() => {
  if (!form.value.ward_id) return null
  return wards.value.find((w) => w.code === Number(form.value.ward_id)) || null
})

const selectedBank = computed(() => {
  if (!form.value.bank_name) return null
  return banks.value.find((b) => b.short_name === form.value.bank_name || b.name === form.value.bank_name) || null
})

async function handleProvinceChange(provinceCode?: number | null) {
  form.value.ward_id = undefined
  wards.value = []
  if (!provinceCode) return

  isLoadingWards.value = true
  try {
    wards.value = await supplierStore.fetchWards(provinceCode)
  } catch (err: any) {
    console.error('Lỗi tải danh sách phường xã:', err)
  } finally {
    isLoadingWards.value = false
  }
}

function filterOptionByName(input: string, option: any) {
  const label = option.label || option.name || ''
  return label.toLowerCase().includes(input.toLowerCase())
}

function filterBankOption(input: string, option: any) {
  const text = `${option.label || ''} ${option.value || ''}`.toLowerCase()
  return text.includes(input.toLowerCase())
}

function switchToEditMode() {
  currentMode.value = 'edit'
}

watch(
  () => props.visible,
  async (val) => {
    resetErrors()
    if (val) {
      isLoadingData.value = true
      currentMode.value = props.mode || (props.supplier?.id ? 'edit' : 'create')
      try {
        await Promise.all([
          supplierStore.fetchProvinces(),
          supplierStore.fetchBanks(),
        ])
        provinces.value = supplierStore.provinces
        banks.value = supplierStore.banks

        if (props.supplier) {
          form.value = {
            id: props.supplier.id,
            code: props.supplier.code || '',
            name: props.supplier.name,
            phone: props.supplier.phone || '',
            email: props.supplier.email || '',
            address: props.supplier.address || '',
            province_id: props.supplier.province_id ? Number(props.supplier.province_id) : undefined,
            ward_id: props.supplier.ward_id ? Number(props.supplier.ward_id) : undefined,
            contact_person: props.supplier.contact_person || '',
            contact_phone: props.supplier.contact_phone || '',
            bank_name: props.supplier.bank_name || undefined,
            bank_account: props.supplier.bank_account || '',
            bank_account_name: props.supplier.bank_account_name || '',
            opening_balance: props.supplier.opening_balance !== undefined && props.supplier.opening_balance !== null
              ? Number(props.supplier.opening_balance)
              : 0,
            note: props.supplier.note || '',
            is_active: props.supplier.is_active ?? true,
          }

          if (form.value.province_id) {
            isLoadingWards.value = true
            try {
              wards.value = await supplierStore.fetchWards(form.value.province_id)
            } finally {
              isLoadingWards.value = false
            }
          } else {
            wards.value = []
          }
        } else {
          form.value = { ...defaultForm }
          wards.value = []
        }
      } catch (err: any) {
        console.error('Lỗi khi tải dữ liệu ban đầu:', err)
      } finally {
        isLoadingData.value = false
      }
    }
  },
  { immediate: true }
)

function validate(): boolean {
  resetErrors()
  const result = supplierSchema.safeParse(form.value)
  if (!result.success) {
    result.error.issues.forEach((issue) => {
      const field = issue.path[0] as string
      if (field && !errors[field]) {
        errors[field] = issue.message
      }
    })
    return false
  }
  return true
}

async function handleSubmit() {
  if (isView.value) return
  if (!validate()) {
    return
  }

  isSubmitting.value = true
  try {
    const payload: SupplierFormData = {
      ...form.value,
      name: form.value.name.trim(),
      code: isEdit.value && form.value.code ? form.value.code.trim() : undefined,
      phone: form.value.phone?.trim() || '',
      email: form.value.email?.trim() || '',
      address: form.value.address?.trim() || '',
      province_id: form.value.province_id || null,
      ward_id: form.value.ward_id || null,
      contact_person: form.value.contact_person?.trim() || '',
      contact_phone: form.value.contact_phone?.trim() || '',
      bank_name: form.value.bank_name?.trim() || '',
      bank_account: form.value.bank_account?.trim() || '',
      bank_account_name: form.value.bank_account_name?.trim() || '',
      opening_balance: Number(form.value.opening_balance) || 0,
      note: form.value.note?.trim() || '',
      is_active: Boolean(form.value.is_active),
    }

    if (isEdit.value && props.supplier?.id) {
      await supplierStore.updateSupplier(props.supplier.id, payload)
      toast.success(t('supplier.updateSuccess', { name: form.value.name }))
    } else {
      await supplierStore.createSupplier(payload)
      toast.success(t('supplier.createSuccess', { name: form.value.name }))
    }
    emit('update:visible', false)
    emit('success')
  } catch (err: any) {
    const apiErrors = err.response?.data?.errors
    if (apiErrors) {
      Object.keys(apiErrors).forEach((key) => {
        errors[key] = Array.isArray(apiErrors[key]) ? apiErrors[key][0] : apiErrors[key]
      })
    }
    const message = err.response?.data?.message || err.message || t('supplier.saveError')
    toast.error(message)
  } finally {
    isSubmitting.value = false
  }
}
</script>

<template>
  <a-modal
    :open="visible"
    :title="null"
    :footer="null"
    :width="740"
    destroyOnClose
    centered
    @cancel="emit('update:visible', false)"
  >
    <div class="flex flex-col max-h-[80vh]">
      <!-- Modal Header (Fixed) -->
      <div class="pb-3 border-b border-slate-100 flex items-center justify-between shrink-0 pr-8 min-h-[32px]">
        <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-normal m-0">
          {{ isView ? t('supplier.modalDetailTitle') : isEdit ? t('supplier.modalEditTitle') : t('supplier.modalCreateTitle') }}
        </h3>
      </div>

      <!-- Loading State khi mở modal: Mặc định của Ant Design -->
      <div v-if="isLoadingData" class="py-24 flex items-center justify-center">
        <a-spin size="large" />
      </div>

      <template v-else>
        <!-- ================= VIEW MODE (CHỈ HIỆN THÔNG TIN, KHÔNG HIỆN INPUT) ================= -->
        <div v-if="isView" class="flex-1 overflow-y-auto px-3 py-3.5 space-y-3.5">
        <!-- 1. Thông tin cơ bản -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
            {{ t('supplier.sectionBasic') }}
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.nameLabel') }}</span>
              <span class="font-semibold text-slate-800 text-base">{{ form.name || '—' }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.colStatus') }}</span>
              <span
                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                :class="form.is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-600'"
              >
                {{ form.is_active ? t('supplier.statusActive') : t('supplier.statusInactive') }}
              </span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.phoneLabel') }}</span>
              <a v-if="form.phone" :href="`tel:${form.phone}`" class="font-medium text-sky-600 hover:underline">
                {{ form.phone }}
              </a>
              <span v-else class="text-slate-400">—</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.emailLabel') }}</span>
              <a v-if="form.email" :href="`mailto:${form.email}`" class="font-medium text-sky-600 hover:underline">
                {{ form.email }}
              </a>
              <span v-else class="text-slate-400">—</span>
            </div>
          </div>
        </div>

        <!-- 2. Người liên hệ -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            {{ t('supplier.sectionContact') }}
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.contactPersonLabel') }}</span>
              <span class="font-medium text-slate-800">{{ form.contact_person || '—' }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.contactPhoneLabel') }}</span>
              <span class="font-medium text-slate-800">{{ form.contact_phone || '—' }}</span>
            </div>
          </div>
        </div>

        <!-- 3. Địa chỉ -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            {{ t('supplier.addressLabel') }}
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm mb-2.5">
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.provinceLabel') }}</span>
              <span class="font-medium text-slate-800">{{ selectedProvince?.name || (props.supplier?.province?.name || '—') }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.wardLabel') }}</span>
              <span class="font-medium text-slate-800">{{ selectedWard?.name || (props.supplier?.ward?.name || '—') }}</span>
            </div>
          </div>
          <div class="text-sm">
            <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.addressLabel') }}</span>
            <span class="font-medium text-slate-800 leading-relaxed">{{ form.address || '—' }}</span>
          </div>
        </div>

        <!-- 4. Tài khoản ngân hàng -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            {{ t('supplier.sectionBank') }}
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3 text-sm">
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.bankNameLabel') }}</span>
              <div class="flex items-center gap-2 mt-0.5">
                <img v-if="selectedBank?.logo" :src="selectedBank.logo" class="w-5 h-5 object-contain rounded shrink-0" alt="logo" />
                <span class="font-medium text-slate-800">{{ form.bank_name || '—' }}</span>
              </div>
            </div>
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.bankAccountLabel') }}</span>
              <span class="font-mono font-bold text-sky-700 text-sm">{{ form.bank_account || '—' }}</span>
            </div>
            <div>
              <span class="text-xs text-slate-400 block mb-0.5">{{ t('supplier.bankAccountNameLabel') }}</span>
              <span class="font-semibold text-slate-800 uppercase text-xs">{{ form.bank_account_name || '—' }}</span>
            </div>
          </div>
        </div>

        <!-- 5. Ghi chú -->
        <div v-if="form.note" class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2 mb-2">
            <span class="w-2 h-2 rounded-full bg-violet-500"></span>
            {{ t('supplier.noteLabel') }}
          </h4>
          <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ form.note }}</p>
        </div>
      </div>

      <!-- ================= EDIT / CREATE MODE (FORM INPUTS) ================= -->
      <form
        v-else
        @submit.prevent="handleSubmit"
        id="supplier-form"
        class="flex-1 overflow-y-auto px-3 py-3.5 space-y-3.5"
      >
        <!-- 1. Thông tin cơ bản -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-sky-500"></span>
            {{ t('supplier.sectionBasic') }}
          </h4>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div v-if="isEdit">
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.codeLabel') }}
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

            <div :class="{ 'md:col-span-2': !isEdit }">
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.nameLabel') }} <span class="text-rose-500">*</span>
              </label>
              <a-input
                v-model:value="form.name"
                :placeholder="t('supplier.namePlaceholder')"
                size="middle"
                maxlength="200"
                :status="errors.name ? 'error' : ''"
                class="!rounded-lg !text-sm"
                @input="clearError('name')"
              >
                <template #prefix><TagOutlined class="text-slate-400 text-sm mr-0.5" /></template>
              </a-input>
              <span v-if="errors.name" class="block text-xs text-rose-500 font-medium mt-1">
                {{ errors.name }}
              </span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.phoneLabel') }}
              </label>
              <a-input
                v-model:value="form.phone"
                :placeholder="t('supplier.phonePlaceholder')"
                size="middle"
                maxlength="20"
                :status="errors.phone ? 'error' : ''"
                class="!rounded-lg !text-sm"
                @input="clearError('phone')"
              >
                <template #prefix><PhoneOutlined class="text-slate-400 text-sm mr-0.5" /></template>
              </a-input>
              <span v-if="errors.phone" class="block text-xs text-rose-500 font-medium mt-1">
                {{ errors.phone }}
              </span>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.emailLabel') }}
              </label>
              <a-input
                v-model:value="form.email"
                :placeholder="t('supplier.emailPlaceholder')"
                size="middle"
                maxlength="150"
                :status="errors.email ? 'error' : ''"
                class="!rounded-lg !text-sm"
                @input="clearError('email')"
              >
                <template #prefix><MailOutlined class="text-slate-400 text-sm mr-0.5" /></template>
              </a-input>
              <span v-if="errors.email" class="block text-xs text-rose-500 font-medium mt-1">
                {{ errors.email }}
              </span>
            </div>
          </div>
        </div>

        <!-- 2. Người liên hệ -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
            {{ t('supplier.sectionContact') }}
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.contactPersonLabel') }}
              </label>
              <a-input
                v-model:value="form.contact_person"
                :placeholder="t('supplier.contactPersonPlaceholder')"
                size="middle"
                maxlength="150"
                class="!rounded-lg !text-sm"
              >
                <template #prefix><UserOutlined class="text-slate-400 text-sm mr-0.5" /></template>
              </a-input>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.contactPhoneLabel') }}
              </label>
              <a-input
                v-model:value="form.contact_phone"
                :placeholder="t('supplier.contactPhonePlaceholder')"
                size="middle"
                maxlength="20"
                class="!rounded-lg !text-sm"
              >
                <template #prefix><PhoneOutlined class="text-slate-400 text-sm mr-0.5" /></template>
              </a-input>
            </div>
          </div>
        </div>

        <!-- 3. Địa chỉ (Đã bỏ Mã số thuế và Quận huyện) -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
            {{ t('supplier.sectionTaxAddress') }}
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.provinceLabel') }}
              </label>
              <a-select
                v-model:value="form.province_id"
                :placeholder="t('supplier.provincePlaceholder')"
                size="middle"
                class="w-full !rounded-lg !text-sm"
                allow-clear
                show-search
                :filter-option="filterOptionByName"
                @change="handleProvinceChange"
              >
                <a-select-option
                  v-for="prov in provinces"
                  :key="prov.code"
                  :value="prov.code"
                  :label="prov.name"
                >
                  {{ prov.name }}
                </a-select-option>
              </a-select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.wardLabel') }}
              </label>
              <a-select
                v-model:value="form.ward_id"
                :placeholder="form.province_id ? t('supplier.wardPlaceholder') : 'Chọn tỉnh thành trước'"
                :disabled="!form.province_id"
                :loading="isLoadingWards"
                size="middle"
                class="w-full !rounded-lg !text-sm"
                allow-clear
                show-search
                :filter-option="filterOptionByName"
              >
                <a-select-option
                  v-for="ward in wards"
                  :key="ward.code"
                  :value="ward.code"
                  :label="ward.name"
                >
                  {{ ward.name }}
                </a-select-option>
              </a-select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1">
              {{ t('supplier.addressLabel') }}
            </label>
            <a-input
              v-model:value="form.address"
              :placeholder="t('supplier.addressPlaceholder')"
              size="middle"
              maxlength="255"
              class="!rounded-lg !text-sm"
            >
              <template #prefix><HomeOutlined class="text-slate-400 text-sm mr-0.5" /></template>
            </a-input>
          </div>
        </div>

        <!-- 4. Tài khoản ngân hàng (Đồng bộ VietQR) -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
            {{ t('supplier.sectionBank') }}
          </h4>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.bankNameLabel') }}
              </label>
              <a-select
                v-model:value="form.bank_name"
                :placeholder="t('supplier.bankNamePlaceholder')"
                size="middle"
                class="w-full !rounded-lg !text-sm"
                allow-clear
                show-search
                :filter-option="filterBankOption"
              >
                <a-select-option
                  v-for="b in banks"
                  :key="b.id"
                  :value="b.short_name || b.name"
                  :label="`${b.short_name ? b.short_name + ' - ' : ''}${b.name}`"
                >
                  <div class="flex items-center gap-2">
                    <img v-if="b.logo" :src="b.logo" class="w-5 h-5 object-contain rounded shrink-0" :alt="b.code || ''" />
                    <span class="font-medium text-xs truncate">{{ b.short_name || b.name }}</span>
                    <span v-if="b.short_name && b.code" class="text-[10px] text-slate-400 font-mono">({{ b.code }})</span>
                  </div>
                </a-select-option>
              </a-select>
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.bankAccountLabel') }}
              </label>
              <a-input
                v-model:value="form.bank_account"
                :placeholder="t('supplier.bankAccountPlaceholder')"
                size="middle"
                maxlength="50"
                class="!rounded-lg font-mono !text-sm"
              />
            </div>

            <div>
              <label class="block text-xs font-semibold text-slate-700 mb-1">
                {{ t('supplier.bankAccountNameLabel') }}
              </label>
              <a-input
                v-model:value="form.bank_account_name"
                :placeholder="t('supplier.bankAccountNamePlaceholder')"
                size="middle"
                maxlength="150"
                class="!rounded-lg !text-sm uppercase"
              />
            </div>
          </div>
        </div>

        <!-- 5. Ghi chú -->
        <div class="bg-slate-50/70 rounded-xl border border-slate-200/70 p-3.5">
          <h4 class="text-sm font-bold text-slate-800 flex items-center gap-2 mb-2">
            <span class="w-2 h-2 rounded-full bg-violet-500"></span>
            {{ t('supplier.noteLabel') }}
          </h4>
          <div>
            <a-textarea
              v-model:value="form.note"
              :rows="2"
              :placeholder="t('supplier.notePlaceholder')"
              class="!rounded-lg !text-sm !p-2.5"
            />
          </div>
        </div>
      </form>
      </template>

      <!-- Modal Footer (Fixed at bottom) -->
      <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 shrink-0 px-3">
        <!-- View mode footer buttons -->
        <template v-if="isView">
          <button
            type="button"
            class="px-4 h-9 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium transition-colors cursor-pointer"
            @click="emit('update:visible', false)"
          >
            {{ t('supplier.cancelModal') }}
          </button>
          <button
            type="button"
            @click="switchToEditMode"
            :disabled="isLoadingData"
            class="inline-flex items-center justify-center px-4 h-9 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50"
          >
            <span class="text-white leading-none">{{ t('supplier.editBtn') }}</span>
          </button>
        </template>

        <!-- Edit/Create mode footer buttons -->
        <template v-else>
          <button
            type="button"
            class="px-4 h-9 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium transition-colors cursor-pointer"
            @click="emit('update:visible', false)"
          >
            {{ t('supplier.cancelModal') }}
          </button>
          <button
            type="submit"
            form="supplier-form"
            :disabled="isLoadingData || isSubmitting"
            class="inline-flex items-center justify-center gap-2 px-5 h-9 rounded-lg bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-sm font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
          >
            <span v-if="isSubmitting" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
            <span class="!text-white text-white leading-none">
              {{ isEdit ? t('supplier.submitUpdate') : t('supplier.submitCreate') }}
            </span>
          </button>
        </template>
      </div>
    </div>
  </a-modal>
</template>
