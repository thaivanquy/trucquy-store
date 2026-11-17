<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import {
  TagOutlined,
  BarcodeOutlined,
} from '@ant-design/icons-vue'
import type { Unit, UnitFormData } from '../types'
import { unitSchema } from '../types'
import { useUnitStore } from '../stores/unitStore'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'
import { breakpointsTailwind, useBreakpoints } from '@vueuse/core'

interface Props {
  visible: boolean
  unit?: Unit | null
  existingUnits?: Unit[]
}

const props = withDefaults(defineProps<Props>(), {
  unit: null,
  existingUnits: () => [],
})

interface Emits {
  (e: 'update:visible', val: boolean): void
  (e: 'success'): void
}

const emit = defineEmits<Emits>()

const toast = useToast()
const { t } = useI18n()
const unitStore = useUnitStore()
const isSubmitting = ref(false)

const breakpoints = useBreakpoints(breakpointsTailwind)
const drawerWidth = computed(() => {
  if (breakpoints.smaller('md').value) return '100%'
  if (breakpoints.smaller('lg').value) return '75%'
  return 500
})

const form = ref<UnitFormData>({
  code: '',
  name: '',
  description: '',
  sort_order: 1,
  is_active: true,
})

const errors = reactive<Record<string, string>>({
  name: '',
  code: '',
  description: '',
})

function resetErrors() {
  errors.name = ''
  errors.code = ''
  errors.description = ''
}

function clearError(field: string) {
  if (errors[field]) {
    errors[field] = ''
  }
}

const isEdit = computed(() => !!props.unit?.id)

watch(
  () => props.visible,
  (val) => {
    resetErrors()
    if (val) {
      if (props.unit) {
        form.value = {
          id: props.unit.id,
          code: props.unit.code,
          name: props.unit.name,
          description: props.unit.description || '',
          sort_order: props.unit.sort_order ?? 1,
          is_active: Boolean(props.unit.is_active),
        }
      } else {
        const nextSortOrder = props.existingUnits.length > 0
          ? Math.max(...props.existingUnits.map((u) => u.sort_order || 0)) + 1
          : 1

        form.value = {
          code: '',
          name: '',
          description: '',
          sort_order: nextSortOrder,
          is_active: true,
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

  const payload: UnitFormData = {
    code: isEdit.value ? form.value.code?.trim() || undefined : undefined,
    name: form.value.name.trim(),
    description: form.value.description?.trim() || null,
    sort_order: Number(form.value.sort_order) || 1,
    is_active: form.value.is_active ?? true,
  }

  const result = unitSchema.safeParse({
    ...payload,
    id: props.unit?.id,
  })

  if (!result.success) {
    result.error.issues.forEach((err) => {
      const field = String(err.path[0])
      if (field in errors) {
        errors[field] = err.message
      }
    })
    return
  }

  isSubmitting.value = true
  try {
    if (isEdit.value && props.unit?.id) {
      await unitStore.updateUnit(props.unit.id, payload)
      toast.success(t('unit.updateSuccess', { name: payload.name }))
    } else {
      await unitStore.createUnit(payload)
      toast.success(t('unit.createSuccess', { name: payload.name }))
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
      t('unit.saveError')
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
    :width="drawerWidth"
    :closable="false"
    :mask-closable="true"
    destroy-on-close
    :z-index="1050"
    :body-style="{ padding: 0, height: '100%', display: 'flex', flexDirection: 'column' }"
    :header-style="{ display: 'none' }"
    :footer-style="{ display: 'none' }"
    class="custom-unit-drawer"
    @close="handleCancel"
  >
    <div class="bg-white flex flex-col h-full">

      <div class="flex items-center justify-between px-2 sm:px-6 py-3.5 sm:py-4 border-b border-slate-100 shrink-0">
        <h3 class="text-base font-bold text-slate-900 leading-tight">
          {{ isEdit ? t('unit.modalEditTitle') : t('unit.modalCreateTitle') }}
        </h3>
        <button
          type="button"
          @click="handleCancel"
          class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer text-lg leading-none"
        >
          &times;
        </button>
      </div>

      <form
        @submit.prevent="handleSubmit"
        id="unit-form"
        class="flex-1 overflow-y-auto px-2 sm:px-6 py-4 space-y-4"
      >

        <div v-if="isEdit">
          <label class="block text-xs font-semibold text-slate-700 mb-1">
            {{ t('unit.codeLabel') }}
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
            {{ t('unit.nameLabel') }} <span class="text-rose-500">*</span>
          </label>
          <a-input
            v-model:value="form.name"
            :placeholder="t('unit.namePlaceholder')"
            size="middle"
            maxlength="100"
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
            {{ t('unit.descriptionLabel') }}
          </label>
          <a-textarea
            v-model:value="form.description"
            :placeholder="t('unit.descriptionPlaceholder')"
            :rows="3"
            size="middle"
            maxlength="500"
            show-count
            class="!rounded-lg !text-sm"
            @input="clearError('description')"
          />
          <span v-if="errors.description" class="block text-xs text-rose-500 font-medium mt-1">
            {{ errors.description }}
          </span>
        </div>
      </form>

      <div class="flex items-center justify-end gap-2.5 px-2 sm:px-6 py-3 border-t border-slate-100 bg-white shrink-0 mt-auto">
        <button
          type="button"
          @click="handleCancel"
          class="px-4 h-9 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition-colors cursor-pointer select-none"
        >
          {{ t('unit.cancelBtn') }}
        </button>

        <button
          type="submit"
          form="unit-form"
          :disabled="isSubmitting"
          class="inline-flex items-center justify-center gap-2 px-5 h-9 rounded-lg bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-xs font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed select-none"
        >
          <span v-if="isSubmitting" class="w-3.5 h-3.5 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
          <span>{{ isEdit ? t('unit.saveBtn') : t('unit.createBtn') }}</span>
        </button>
      </div>
    </div>
  </a-drawer>
</template>
