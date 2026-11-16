<script setup lang="ts">
interface Props {
  brandName?: string
  class?: string
}

withDefaults(defineProps<Props>(), {
  brandName: 'TrucQuy',
})

const currentYear = new Date().getFullYear()
</script>

<template>
  <footer
    :class="[
      'ant-layout-footer !py-3 !px-4 sm:!px-6 !bg-white border-t border-slate-200/80 text-center text-xs text-slate-400 leading-relaxed select-none cursor-default pointer-events-none',
      $props.class,
    ]"
  >
    <slot>
      © {{ currentYear }} {{ brandName }}
    </slot>
  </footer>
</template>
