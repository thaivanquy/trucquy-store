<script setup lang="ts">
import { TagOutlined } from '@ant-design/icons-vue'
import type { Unit } from '../types'
import { useI18n } from '@/i18n'

const { t } = useI18n()

interface Props {
  visible: boolean
  unit?: Unit | null
}

const props = withDefaults(defineProps<Props>(), {
  unit: null,
})

interface Emits {
  (e: 'update:visible', val: boolean): void
  (e: 'edit', unit: Unit): void
}

const emit = defineEmits<Emits>()

function handleClose() {
  emit('update:visible', false)
}

function handleEdit() {
  if (props.unit) {
    emit('edit', props.unit)
    handleClose()
  }
}
</script>

<template>
  <a-drawer
    :open="visible"
    placement="right"
    :width="500"
    :closable="false"
    :mask-closable="true"
    :z-index="1050"
    :body-style="{ padding: 0, height: '100%', display: 'flex', flexDirection: 'column' }"
    :header-style="{ display: 'none' }"
    :footer-style="{ display: 'none' }"
    class="custom-unit-detail-drawer"
    @close="handleClose"
  >
    <div v-if="unit" class="bg-white flex flex-col h-full text-slate-800">

      <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 shrink-0 bg-white">
        <h3 class="text-base font-bold text-slate-900 leading-tight">
          {{ t('unit.modalDetailTitle') }}
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

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-2xs space-y-3">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center text-lg font-bold">
              <TagOutlined />
            </div>
            <div>
              <h4 class="text-lg font-bold text-slate-900 leading-snug">
                {{ unit.name }}
              </h4>
              <span class="font-mono text-xs font-bold text-sky-700 bg-sky-50 border border-sky-200 px-2 py-0.5 rounded-md">
                {{ t('unit.codePrefix', { code: unit.code }) }}
              </span>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-3">
          <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-400 block">{{ t('unit.priorityOrder') }}</span>
            <span class="text-base font-bold text-slate-800 block mt-1">#{{ unit.sort_order }}</span>
          </div>

          <div class="bg-white p-3.5 rounded-xl border border-slate-200/80 shadow-2xs">
            <span class="text-xs font-semibold text-slate-400 block">{{ t('unit.identifierCode') }}</span>
            <span class="text-base font-mono font-bold text-slate-800 block mt-1">{{ unit.code }}</span>
          </div>
        </div>

        <div v-if="unit.description" class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs space-y-1.5">
          <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wide">
            {{ t('unit.descSectionTitle') }}
          </h5>
          <p class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed whitespace-pre-line">
            {{ unit.description }}
          </p>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2.5 px-6 py-4 border-t border-slate-100 bg-white shrink-0">
        <button
          type="button"
          @click="handleClose"
          class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-semibold transition-colors cursor-pointer"
        >
          {{ t('unit.closeModal') }}
        </button>

        <button
          type="button"
          @click="handleEdit"
          class="inline-flex items-center justify-center px-4.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-700 !text-white text-xs font-semibold shadow-xs transition-colors cursor-pointer"
        >
          <span class="!text-white">{{ t('unit.editBtn') }}</span>
        </button>
      </div>
    </div>
  </a-drawer>
</template>
