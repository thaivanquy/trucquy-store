<script setup lang="ts">
import { computed } from 'vue'
import { GlobalOutlined, DownOutlined, CheckOutlined } from '@ant-design/icons-vue'
import { useI18n } from '@/i18n'

export interface LanguageOption {
  code: string
  label: string
  flag?: string
}

withDefaults(
  defineProps<{
    languages?: LanguageOption[]
  }>(),
  {
    languages: () => [
      { code: 'VI', label: 'Tiếng Việt', flag: 'VI' },
      { code: 'EN', label: 'English', flag: 'EN' },
    ],
  },
)

const emit = defineEmits<{
  (e: 'change', code: string): void
}>()

const { locale, setLocale } = useI18n()

const activeCode = computed(() => locale.value)

function selectLanguage(code: string) {
  setLocale(code)
  emit('change', code)
}
</script>

<template>
  <div class="inline-flex items-center leading-normal">
    <a-dropdown :trigger="['click']" placement="bottomRight">
      <button
        type="button"
        class="flex items-center gap-1.5 py-1.5 px-2.5 rounded-lg text-slate-600 hover:text-sky-600 hover:bg-sky-50 transition-colors cursor-pointer select-none font-medium text-xs sm:text-sm border border-slate-200/80 bg-white leading-normal shadow-xs"
        aria-label="Chọn ngôn ngữ"
      >
        <GlobalOutlined class="text-sm text-slate-400" />
        <span class="font-semibold text-slate-700">{{ activeCode }}</span>
        <DownOutlined class="text-[10px] text-slate-400" />
      </button>

      <template #overlay>
        <a-menu class="!rounded-xl !p-1.5 !min-w-[140px] !shadow-lg !border !border-slate-100">
          <a-menu-item
            v-for="lang in languages"
            :key="lang.code"
            @click="selectLanguage(lang.code)"
            :class="{ '!bg-sky-50 !text-sky-600 font-semibold': activeCode === lang.code }"
            class="!rounded-lg !py-1.5"
          >
            <div class="flex items-center justify-between gap-3 text-xs">
              <div class="flex items-center gap-2">
                <span
                  class="text-[10px] font-bold px-1.5 py-0.5 rounded leading-none tracking-wider"
                  :class="activeCode === lang.code ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600'"
                >
                  {{ lang.flag || lang.code }}
                </span>
                <span>{{ lang.label }}</span>
              </div>
              <CheckOutlined v-if="activeCode === lang.code" class="text-sky-500 text-xs shrink-0" />
            </div>
          </a-menu-item>
        </a-menu>
      </template>
    </a-dropdown>
  </div>
</template>
