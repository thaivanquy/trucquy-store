<script setup lang="ts">
import { useI18n } from '@/i18n'

const { t } = useI18n()
</script>

<template>
  <div class="space-y-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-slate-800">{{ t('admin.overview') }}</h1>
        <p class="text-sm text-slate-500 mt-1">{{ t('admin.welcome') }}</p>
      </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ t('admin.todayRevenue') }}</p>
        <p class="text-2xl font-bold text-slate-800 mt-2">0 ₫</p>
        <span class="text-xs text-emerald-600 font-medium mt-1 inline-block">{{ t('admin.todayRevenueGrowth') }}</span>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ t('admin.newOrders') }}</p>
        <p class="text-2xl font-bold text-slate-800 mt-2">0</p>
        <span class="text-xs text-slate-400 mt-1 inline-block">{{ t('admin.pending') }}</span>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ t('admin.products') }}</p>
        <p class="text-2xl font-bold text-slate-800 mt-2">0</p>
        <span class="text-xs text-slate-400 mt-1 inline-block">{{ t('admin.activeSelling') }}</span>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm">
        <p class="text-xs font-medium text-slate-400 uppercase tracking-wider">{{ t('admin.customers') }}</p>
        <p class="text-2xl font-bold text-slate-800 mt-2">0</p>
        <span class="text-xs text-slate-400 mt-1 inline-block">{{ t('admin.members') }}</span>
      </div>
    </div>
  </div>
</template>
