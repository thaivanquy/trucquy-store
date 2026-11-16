<script setup lang="ts">
import { ref, reactive, computed, watch } from 'vue'
import {
  TagOutlined,
  BarcodeOutlined,
  LinkOutlined,
  ApartmentOutlined,
} from '@ant-design/icons-vue'
import type { Category, CategoryFormData } from '../types'
import { categorySchema } from '../types'
import { useCategoryStore } from '../stores/categoryStore'
import AppImageUpload from '@/components/common/AppImageUpload.vue'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'
import { toSlug, generateCategoryCode } from '@/utils/generator'

interface Props {
  visible: boolean;
  category?: Category | null;
  categories: Category[];
  defaultParentId?: number | null;
}

const props = defineProps<Props>()

interface Emits {
  (e: 'update:visible', val: boolean): void;
  (e: 'success'): void;
}

const emit = defineEmits<Emits>()

const toast = useToast()
const { t } = useI18n()
const categoryStore = useCategoryStore()
const isSubmitting = ref(false)

const form = ref<CategoryFormData>({
  parent_id: null,
  code: '',
  name: '',
  slug: '',
  description: '',
  image: '',
  sort_order: 1,
  is_active: true,
})

const errors = reactive<Record<string, string>>({
  name: '',
  code: '',
  slug: '',
  parent_id: '',
  description: '',
})

function resetErrors() {
  errors.name = ''
  errors.code = ''
  errors.slug = ''
  errors.parent_id = ''
  errors.description = ''
}

function clearError(field: string) {
  if (errors[field]) {
    errors[field] = ''
  }
}

const isEdit = computed(() => !!props.category?.id)

const hasChildren = computed(() => {
  if (!props.category?.id) return false
  return props.categories.some((c) => c.parent_id === props.category?.id)
})

const isParentDisabled = computed(() => {
  if (props.defaultParentId !== null && props.defaultParentId !== undefined) return true
  if (isEdit.value && hasChildren.value) return true
  return false
})

const parentOptions = computed(() => {
  const currentId = props.category?.id
  const filtered = props.categories.filter((c) => {
    if (currentId && c.id === currentId) return false
    return !c.parent_id
  })

  return [
    { label: t('category.noParentOption'), value: null },
    ...filtered.map((c) => ({
      label: `${c.name} (${c.code})`,
      value: c.id,
    })),
  ]
})

function handleAutoGenerate() {
  clearError('name')
  clearError('code')
  clearError('slug')

  if (!isEdit.value && form.value.name) {
    form.value.slug = toSlug(form.value.name)
    form.value.code = generateCategoryCode(
      form.value.name,
      form.value.parent_id,
      props.categories
    )
  }
}

watch(
  () => props.visible,
  (val) => {
    resetErrors()
    if (val) {
      if (props.category) {
        form.value = {
          id: props.category.id,
          parent_id: props.category.parent_id,
          code: props.category.code,
          name: props.category.name,
          slug: props.category.slug,
          description: props.category.description || '',
          image: props.category.image || '',
          sort_order: props.category.sort_order ?? 1,
          is_active: props.category.is_active,
        }
      } else {
        form.value = {
          parent_id: props.defaultParentId ?? null,
          code: '',
          name: '',
          slug: '',
          description: '',
          image: '',
          sort_order: props.categories.length + 1,
          is_active: true,
        }
      }
    }
  },
  { immediate: true }
)

function validate(): boolean {
  resetErrors()
  const result = categorySchema.safeParse(form.value)
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
  if (!validate()) {
    return
  }

  isSubmitting.value = true
  try {
    if (isEdit.value && props.category?.id) {
      await categoryStore.updateCategory(props.category.id, form.value)
      toast.success(t('category.updateSuccess', { name: form.value.name }))
    } else {
      await categoryStore.createCategory(form.value)
      toast.success(t('category.createSuccess', { name: form.value.name }))
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
    const message = err.response?.data?.message || err.message || t('category.saveError')
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
    :width="600"
    destroyOnClose
    centered
    @cancel="emit('update:visible', false)"
  >
    <div class="p-1">
      <div class="pb-3 mb-3.5 border-b border-slate-100 flex items-center justify-between pr-8 min-h-[32px]">
        <h3 class="text-base sm:text-lg font-bold text-slate-900 leading-normal m-0">
          {{ isEdit ? t('category.modalEditTitle') : t('category.modalCreateTitle') }}
        </h3>
      </div>

      <form @submit.prevent="handleSubmit" class="space-y-3.5">
        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1.5">
            {{ t('category.imageLabel') }}
          </label>
          <AppImageUpload
            v-model="form.image"
            :placeholder="t('category.imageUploadPlaceholder')"
            :helpText="t('category.imageHelpText')"
          />
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">
            {{ t('category.nameLabel') }} <span class="text-rose-500">*</span>
          </label>
          <a-input
            v-model:value="form.name"
            :placeholder="t('category.namePlaceholder')"
            size="middle"
            maxlength="150"
            :status="errors.name ? 'error' : ''"
            class="!rounded-lg !text-sm"
            @input="handleAutoGenerate"
            @change="handleAutoGenerate"
          >
            <template #prefix><TagOutlined class="text-slate-400 text-sm mr-0.5" /></template>
          </a-input>
          <span v-if="errors.name" class="block text-xs text-rose-500 font-medium mt-1">
            {{ errors.name }}
          </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
              {{ t('category.codeLabel') }}
            </label>
            <a-input
              v-model:value="form.code"
              disabled
              :placeholder="t('category.codePlaceholder')"
              size="middle"
              maxlength="50"
              :status="errors.code ? 'error' : ''"
              class="!rounded-lg font-mono uppercase !text-sm !bg-slate-50 !text-slate-800 !border-slate-200 cursor-not-allowed"
            >
              <template #prefix><BarcodeOutlined class="text-slate-400 text-sm mr-0.5" /></template>
            </a-input>
            <span v-if="errors.code" class="block text-xs text-rose-500 font-medium mt-1 font-sans">
              {{ errors.code }}
            </span>
          </div>

          <div>
            <label class="block text-sm font-semibold text-slate-700 mb-1">
              {{ t('category.slugLabel') }}
            </label>
            <a-input
              v-model:value="form.slug"
              disabled
              :placeholder="t('category.slugPlaceholder')"
              size="middle"
              maxlength="180"
              :status="errors.slug ? 'error' : ''"
              class="!rounded-lg font-mono !text-sm !bg-slate-50 !text-slate-700 !border-slate-200 cursor-not-allowed"
            >
              <template #prefix><LinkOutlined class="text-slate-400 text-sm mr-0.5" /></template>
            </a-input>
            <span v-if="errors.slug" class="block text-xs text-rose-500 font-medium mt-1 font-sans">
              {{ errors.slug }}
            </span>
          </div>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">
            {{ t('category.parentLabel') }}
          </label>
          <a-select
            v-model:value="form.parent_id"
            :options="parentOptions"
            :placeholder="t('category.parentPlaceholder')"
            size="middle"
            :disabled="isParentDisabled"
            class="w-full !rounded-lg !text-sm"
            @change="handleAutoGenerate"
          >
            <template #suffixIcon><ApartmentOutlined class="text-slate-400 text-sm" /></template>
          </a-select>
          <span v-if="isEdit && hasChildren" class="block text-xs text-amber-600 font-medium mt-1">
            {{ t('category.parentHasChildrenWarning') }}
          </span>
          <span v-if="errors.parent_id" class="block text-xs text-rose-500 font-medium mt-1">
            {{ errors.parent_id }}
          </span>
        </div>

        <div>
          <label class="block text-sm font-semibold text-slate-700 mb-1">
            {{ t('category.descLabel') }}
          </label>
          <a-textarea
            v-model:value="form.description"
            :rows="3"
            :status="errors.description ? 'error' : ''"
            :placeholder="t('category.descPlaceholder')"
            class="!rounded-lg !text-sm !p-2.5"
            @input="clearError('description')"
          />
          <span v-if="errors.description" class="block text-xs text-rose-500 font-medium mt-1">
            {{ errors.description }}
          </span>
        </div>

        <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100 mt-3.5">
          <button
            type="button"
            class="px-4 h-9 rounded-lg border border-slate-200 hover:bg-slate-50 text-slate-700 text-sm font-medium transition-colors cursor-pointer"
            @click="emit('update:visible', false)"
          >
            {{ t('category.cancelModal') }}
          </button>
          <button
            type="submit"
            :disabled="isSubmitting"
            class="inline-flex items-center justify-center px-4.5 h-9 rounded-lg bg-sky-600 hover:bg-sky-700 active:bg-sky-800 !text-white text-sm font-semibold shadow-xs transition-all active:scale-[0.98] cursor-pointer disabled:opacity-60"
          >
            <span class="!text-white text-white leading-none">{{ isEdit ? t('category.submitUpdate') : t('category.submitCreate') }}</span>
          </button>
        </div>
      </form>
    </div>
  </a-modal>
</template>
