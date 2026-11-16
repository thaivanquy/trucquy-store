<script setup lang="ts">
import LoginForm from '../components/LoginForm.vue'
import AppFooter from '@/components/common/AppFooter.vue'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'
import { useI18n } from '@/i18n'

const { t } = useI18n()
</script>

<template>
  <div class="relative min-h-screen w-full flex flex-col justify-between overflow-hidden bg-slate-50/50">
    <div
      class="pointer-events-none absolute -top-40 -left-40 w-96 h-96 sm:w-[500px] sm:h-[500px] rounded-full bg-emerald-200/40 blur-3xl"
    ></div>
    <div
      class="pointer-events-none absolute top-10 left-1/3 w-80 h-80 sm:w-[450px] sm:h-[450px] rounded-full bg-sky-200/50 blur-3xl"
    ></div>
    <div
      class="pointer-events-none absolute top-1/4 -right-20 w-80 h-80 sm:w-[450px] sm:h-[450px] rounded-full bg-green-200/35 blur-3xl"
    ></div>

    <header class="relative z-30 w-full px-6 py-4 sm:px-10 flex items-center justify-between">
      <div class="flex items-center gap-2.5">
        <span class="text-lg font-bold text-slate-800 tracking-tight">{{ t('common.brandName') }}</span>
      </div>

      <div class="flex items-center gap-4 text-xs sm:text-sm text-slate-500 font-medium">
        <LanguageSwitcher />

        <a
          href="#"
          class="flex items-center gap-1.5 hover:text-slate-800 transition-colors py-1 px-2 rounded-lg hover:bg-slate-100"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="12" r="10" />
            <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3" />
            <line x1="12" y1="17" x2="12.01" y2="17" />
          </svg>
          <span>{{ t('common.help') }}</span>
        </a>
      </div>
    </header>

    <main class="relative z-10 flex-1 flex items-center justify-center px-4 py-8 sm:px-6">
      <LoginForm />
    </main>

    <AppFooter class="!bg-transparent !border-t-0" />
  </div>
</template>
