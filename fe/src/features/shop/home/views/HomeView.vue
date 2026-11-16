<script setup lang="ts">
import { useI18n } from '@/i18n'

const { t } = useI18n()
</script>

<template>
  <div class="space-y-8">
    <div class="bg-gradient-to-r from-emerald-600 to-teal-700 rounded-3xl p-8 sm:p-12 text-white">
      <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight">{{ t('shop.welcomeBanner') }}</h1>
      <p class="mt-3 text-emerald-100 max-w-xl text-base sm:text-lg">
        {{ t('shop.welcomeDesc') }}
      </p>
    </div>
  </div>
</template>
