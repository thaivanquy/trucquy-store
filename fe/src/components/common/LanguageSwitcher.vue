<script setup lang="ts">
import { ref, computed } from 'vue'
import { onClickOutside } from '@vueuse/core'
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
const isOpen = ref(false)
const dropdownRef = ref<HTMLElement | null>(null)

const activeCode = computed(() => locale.value)

onClickOutside(dropdownRef, () => {
  isOpen.value = false
})

function toggleDropdown() {
  isOpen.value = !isOpen.value
}

function selectLanguage(code: string) {
  setLocale(code)
  isOpen.value = false
  emit('change', code)
}
</script>

<template>
  <div ref="dropdownRef" class="relative inline-block text-left z-50">
    <button
      type="button"
      @click="toggleDropdown"
      class="flex items-center gap-1.5 py-1 px-2.5 rounded-lg text-slate-500 hover:text-slate-800 hover:bg-slate-100/80 transition-colors cursor-pointer select-none font-medium text-xs sm:text-sm"
      :class="{ 'bg-slate-100 text-slate-800': isOpen }"
      aria-haspopup="true"
      :aria-expanded="isOpen"
    >
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-4 h-4 text-slate-500"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <circle cx="12" cy="12" r="10" />
        <line x1="2" y1="12" x2="22" y2="12" />
        <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" />
      </svg>

      <span>{{ activeCode }}</span>

      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="w-3 h-3 text-slate-400 transition-transform duration-200"
        :class="{ 'rotate-180': isOpen }"
        viewBox="0 0 24 24"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="6 9 12 15 18 9" />
      </svg>
    </button>

    <Transition
      enter-active-class="transition duration-150 ease-out"
      enter-from-class="transform scale-95 opacity-0 -translate-y-1"
      enter-to-class="transform scale-100 opacity-100 translate-y-0"
      leave-active-class="transition duration-100 ease-in"
      leave-from-class="transform scale-100 opacity-100 translate-y-0"
      leave-to-class="transform scale-95 opacity-0 -translate-y-1"
    >
      <div
        v-if="isOpen"
        class="absolute right-0 mt-1.5 w-36 origin-top-right rounded-xl bg-white p-1.5 shadow-lg shadow-slate-200/50 border border-slate-100 z-50 focus:outline-none"
      >
        <button
          v-for="lang in languages"
          :key="lang.code"
          type="button"
          @mousedown.stop.prevent="selectLanguage(lang.code)"
          @click.stop="selectLanguage(lang.code)"
          class="flex items-center justify-between w-full px-2.5 py-1.5 rounded-lg text-xs font-medium transition-colors cursor-pointer select-none"
          :class="[
            activeCode === lang.code
              ? 'bg-sky-50 text-sky-600 font-semibold'
              : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
          ]"
        >
          <div class="flex items-center gap-2">
            <span
              v-if="lang.flag"
              class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-600 border border-slate-200/60 leading-none tracking-wider"
              :class="{ 'bg-sky-100/70 text-sky-700 border-sky-200/60': activeCode === lang.code }"
            >
              {{ lang.flag }}
            </span>
            <span>{{ lang.label }}</span>
          </div>

          <svg
            v-if="activeCode === lang.code"
            xmlns="http://www.w3.org/2000/svg"
            class="w-3.5 h-3.5 text-sky-500"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <polyline points="20 6 9 17 4 12" />
          </svg>
        </button>
      </div>
    </Transition>
  </div>
</template>
