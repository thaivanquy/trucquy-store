<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import LanguageSwitcher from '@/components/common/LanguageSwitcher.vue'
import { useI18n } from '@/i18n'

const { t } = useI18n()
</script>

<template>
  <div class="min-h-screen flex flex-col bg-slate-50 text-slate-800">
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-8">
          <RouterLink to="/" class="flex items-center gap-2">
            <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white font-bold text-sm">
              TQ
            </div>
            <span class="text-lg font-bold text-slate-900 tracking-tight">{{ t('shop.storeName') }}</span>
          </RouterLink>

          <nav class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
            <RouterLink to="/" class="hover:text-emerald-600 transition-colors">{{ t('shop.home') }}</RouterLink>
            <a href="#" class="hover:text-emerald-600 transition-colors">{{ t('shop.products') }}</a>
            <a href="#" class="hover:text-emerald-600 transition-colors">{{ t('shop.promotions') }}</a>
            <a href="#" class="hover:text-emerald-600 transition-colors">{{ t('shop.contact') }}</a>
          </nav>
        </div>

        <div class="flex items-center gap-4">
          <LanguageSwitcher />
          <RouterLink
            to="/admin/login"
            class="text-xs text-slate-500 hover:text-slate-800 transition-colors"
          >
            {{ t('shop.adminChannel') }}
          </RouterLink>
        </div>
      </div>
    </header>

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <RouterView />
    </main>

    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
      <p>© {{ new Date().getFullYear() }} {{ t('shop.storeName') }}. {{ t('common.allRightsReserved') }}</p>
    </footer>
  </div>
</template>
