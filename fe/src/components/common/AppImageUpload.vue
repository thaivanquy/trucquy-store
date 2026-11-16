<script setup lang="ts">
import { ref } from 'vue'
import {
  CloudUploadOutlined,
  DeleteOutlined,
  EyeOutlined,
  PictureOutlined,
} from '@ant-design/icons-vue'
import { useToast } from '@/composables/useToast'
import { useI18n } from '@/i18n'

interface Props {
  modelValue?: string | null;
  placeholder?: string;
  helpText?: string;
  maxSizeMb?: number;
}

const props = withDefaults(
  defineProps<Props>(),
  {
    modelValue: '',
    placeholder: '',
    helpText: '',
    maxSizeMb: 5,
  }
)

interface Emits {
  (e: 'update:modelValue', value: string | null): void;
  (e: 'change', file: File | null): void;
}

const emit = defineEmits<Emits>()

const toast = useToast()
const { t } = useI18n()
const fileInput = ref<HTMLInputElement | null>(null)
const previewVisible = ref<boolean>(false)

function triggerSelectFile() {
  fileInput.value?.click()
}

function handleFileChange(event: Event) {
  const target = event.target as HTMLInputElement
  const file = target.files?.[0]

  if (!file) return

  if (!file.type.startsWith('image/')) {
    toast.error(t('common.invalidImageError'))
    target.value = ''
    return
  }

  const maxBytes = props.maxSizeMb * 1024 * 1024
  if (file.size > maxBytes) {
    toast.error(t('common.imageSizeError', { size: props.maxSizeMb }))
    target.value = ''
    return
  }

  const reader = new FileReader()
  reader.onload = (e) => {
    const result = e.target?.result as string
    emit('update:modelValue', result)
    emit('change', file)
    target.value = ''
  }
  reader.readAsDataURL(file)
}

function handleRemove(e: Event) {
  e.stopPropagation()
  emit('update:modelValue', null)
  emit('change', null)
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}
</script>

<template>
  <div class="w-full">
    <input
      ref="fileInput"
      type="file"
      accept="image/*"
      class="hidden"
      @change="handleFileChange"
    />

    <div
      v-if="!modelValue"
      @click="triggerSelectFile"
      class="group border-2 border-dashed border-slate-200 hover:border-sky-500 rounded-xl p-6 sm:p-7 bg-slate-50/50 hover:bg-sky-50/30 transition-all cursor-pointer flex flex-col items-center justify-center text-center h-40 sm:h-44"
    >
      <div class="w-12 h-12 rounded-xl bg-white group-hover:bg-sky-500 group-hover:text-white text-slate-500 shadow-2xs flex items-center justify-center text-2xl mb-2.5 transition-all">
        <CloudUploadOutlined />
      </div>
      <span class="text-sm font-semibold text-slate-700 group-hover:text-sky-600 transition-colors">
        {{ placeholder || t('common.uploadPlaceholder') }}
      </span>
      <span class="text-xs text-slate-400 mt-1">
        {{ helpText || t('common.uploadHelpText', { size: maxSizeMb }) }}
      </span>
    </div>

    <div
      v-else
      class="relative group rounded-xl overflow-hidden border border-slate-200/90 shadow-2xs w-full h-52 sm:h-64 bg-slate-100 flex items-center justify-center"
    >
      <img
        :src="modelValue"
        alt="Preview"
        class="w-full h-full object-cover object-center"
      />

      <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center gap-2.5">
        <button
          type="button"
          @click="previewVisible = true"
          class="w-9 h-9 rounded-lg bg-white/95 hover:bg-white text-slate-700 shadow-sm flex items-center justify-center transition-transform hover:scale-105 cursor-pointer"
          :title="t('common.viewImage')"
        >
          <EyeOutlined class="text-sm" />
        </button>
        <button
          type="button"
          @click="triggerSelectFile"
          class="px-3.5 h-9 rounded-lg bg-sky-600 hover:bg-sky-700 !text-white text-xs sm:text-sm font-semibold shadow-sm flex items-center gap-1.5 transition-transform hover:scale-105 cursor-pointer select-none"
        >
          <PictureOutlined class="text-xs !text-white text-white" />
          <span class="!text-white text-white leading-none">{{ t('common.changeImage') }}</span>
        </button>
        <button
          type="button"
          @click="handleRemove"
          class="w-9 h-9 rounded-lg bg-rose-500 hover:bg-rose-600 !text-white text-white shadow-sm flex items-center justify-center transition-transform hover:scale-105 cursor-pointer"
          :title="t('common.deleteImage')"
        >
          <DeleteOutlined class="text-sm !text-white text-white" />
        </button>
      </div>
    </div>

    <a-modal
      :open="previewVisible"
      :footer="null"
      centered
      @cancel="previewVisible = false"
    >
      <img
        :src="modelValue || ''"
        alt="Preview Full"
        class="w-full h-auto rounded-lg mt-3"
      />
    </a-modal>
  </div>
</template>
