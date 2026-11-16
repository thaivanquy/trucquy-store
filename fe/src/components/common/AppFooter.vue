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
      'relative z-10 w-full text-center py-4 text-xs text-slate-400',
      $props.class,
    ]"
  >
    <slot>
      © {{ currentYear }} {{ brandName }}
    </slot>
  </footer>
</template>
