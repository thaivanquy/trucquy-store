<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'
import { useI18n } from '@/i18n'

const { t } = useI18n()
</script>

<template>
  <div class="flex h-screen bg-slate-50 overflow-hidden">
    <aside class="w-64 bg-slate-900 text-slate-300 flex flex-col shrink-0">
      <div class="h-16 flex items-center gap-3 px-6 border-b border-slate-800">
        <div class="w-8 h-8 rounded-full bg-emerald-500 flex items-center justify-center text-white font-bold text-sm">
          TQ
        </div>
        <div>
          <span class="font-bold text-white tracking-tight">{{ t('common.brandName') }}</span>
          <span class="block text-[10px] text-emerald-400 font-medium">{{ t('common.adminPortal') }}</span>
        </div>
      </div>

      <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
        <RouterLink
          to="/admin/dashboard"
          class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors"
          active-class="bg-emerald-500/10 text-emerald-400 font-semibold"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
          </svg>
          {{ t('admin.dashboard') }}
        </RouterLink>
      </nav>

      <div class="p-4 border-t border-slate-800">
        <RouterLink
          to="/"
          target="_blank"
          class="flex items-center justify-center gap-2 w-full py-2 px-3 text-xs text-slate-400 hover:text-white rounded-lg hover:bg-slate-800 transition-colors"
        >
          <span>{{ t('common.viewStorefront') }}</span>
          <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
          </svg>
        </RouterLink>
      </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
      <header class="h-16 bg-white border-b border-slate-200 px-6 flex items-center justify-between shrink-0 relative z-20">
        <div class="text-sm text-slate-500 font-medium">{{ t('admin.systemManagement') }}</div>
        <div class="flex items-center gap-3">
          <LanguageSwitcher />
          <RouterLink
            to="/admin/login"
            class="text-xs text-slate-600 hover:text-slate-900 px-3 py-1.5 rounded-lg border border-slate-200 hover:bg-slate-50 transition-colors"
          >
            {{ t('common.logout') }}
          </RouterLink>
        </div>
      </header>

      <main class="flex-1 overflow-y-auto p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>
