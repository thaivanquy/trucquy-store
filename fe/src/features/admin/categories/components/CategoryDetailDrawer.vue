<script setup lang="ts">
import {
  EditOutlined,
  PictureOutlined,
  BranchesOutlined,
} from '@ant-design/icons-vue'
import type { Category } from '../types'
import { useI18n } from '@/i18n'

const { t } = useI18n()

interface Props {
  visible: boolean
  category?: Category | null
}

const props = withDefaults(defineProps<Props>(), {
  category: null,
})

interface Emits {
  (e: 'update:visible', val: boolean): void
  (e: 'edit', category: Category): void
}

const emit = defineEmits<Emits>()

function handleClose() {
  emit('update:visible', false)
}

function handleEdit() {
  if (props.category) {
    emit('edit', props.category)
    handleClose()
  }
}
</script>

<template>
  <a-drawer
    :open="visible"
    placement="right"
    :width="540"
    :closable="false"
    :mask-closable="true"
    :z-index="1050"
    :body-style="{ padding: 0, height: '100%', display: 'flex', flexDirection: 'column' }"
    :header-style="{ display: 'none' }"
    :footer-style="{ display: 'none' }"
    class="custom-category-detail-drawer"
    @close="handleClose"
  >
    <div v-if="category" class="bg-white flex flex-col h-full text-slate-800">

      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0 bg-white">
        <h3 class="text-base font-bold text-slate-900 leading-tight">
          {{ t('category.modalDetailTitle') }}
        </h3>
        <button
          type="button"
          @click="handleClose"
          class="w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 flex items-center justify-center transition-colors cursor-pointer text-lg leading-none"
        >
          &times;
        </button>
      </div>

      <div class="flex-1 p-6 overflow-y-auto space-y-4 bg-slate-50/30">

        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
          <div class="flex items-start gap-3.5">
            <div class="w-20 h-20 rounded-xl bg-slate-50 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
              <img
                v-if="category.image"
                :src="category.image"
                :alt="category.name"
                class="w-full h-full object-cover"
              />
              <div v-else class="text-slate-300 flex flex-col items-center">
                <PictureOutlined class="text-xl mb-0.5 opacity-60" />
                <span class="text-[9px] text-slate-400">{{ t('category.noImage') }}</span>
              </div>
            </div>

            <div class="flex-1 space-y-1.5">
              <h4 class="text-base font-bold text-slate-900 leading-snug">
                {{ category.name }}
              </h4>

              <div class="flex flex-wrap items-center gap-2">
                <span class="font-mono text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200 px-2 py-0.5 rounded-md">
                  {{ t('category.codePrefix', { code: category.code }) }}
                </span>
                <span v-if="category.slug" class="text-xs text-slate-400 font-mono">
                  /{{ category.slug }}
                </span>
              </div>

              <div v-if="category.parent" class="text-xs text-slate-500 pt-0.5 flex items-center gap-1">
                <BranchesOutlined class="text-slate-400" />
                <span>{{ t('category.belongToParent') }}</span>
                <span class="font-semibold text-slate-700">{{ category.parent.name }}</span>
              </div>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-400 block">{{ t('category.displayOrder') }}</span>
            <span class="text-base font-bold text-slate-800 block mt-1">#{{ category.sort_order }}</span>
          </div>

          <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-400 block">{{ t('category.subCategoryCountLabel') }}</span>
            <span class="text-base font-bold text-slate-800 block mt-1">
              {{ t('category.subCategoriesUnit', { count: category.children ? category.children.length : 0 }) }}
            </span>
          </div>
        </div>

        <div v-if="category.children && category.children.length > 0" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-2">
          <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
            {{ t('category.directChildren', { count: category.children.length }) }}
          </h5>
          <div class="space-y-1.5 max-h-48 overflow-y-auto">
            <div
              v-for="sub in category.children"
              :key="sub.id"
              class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100 text-xs"
            >
              <span class="font-medium text-slate-700">{{ sub.name }}</span>
              <span class="font-mono text-slate-400">{{ sub.code }}</span>
            </div>
          </div>
        </div>

        <div v-if="category.description" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-1.5">
          <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
            {{ t('category.descSectionTitle') }}
          </h5>
          <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed whitespace-pre-line">
            {{ category.description }}
          </p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-slate-100 bg-white shrink-0">
        <button
          type="button"
          @click="handleClose"
          class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer"
        >
          {{ t('category.closeModal') }}
        </button>

        <button
          type="button"
          @click="handleEdit"
          class="inline-flex items-center justify-center px-4.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 !text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer"
        >
          <span class="!text-white">{{ t('category.editBtn') }}</span>
        </button>
      </div>
    </div>
  </a-drawer>
</template>
