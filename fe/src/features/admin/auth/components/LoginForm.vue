<script setup lang="ts">
import { Field, ErrorMessage } from 'vee-validate'
import { useLoginForm } from '../composables/useLoginForm'
import { useI18n } from '@/i18n'

const { t } = useI18n()

const {
  showPassword,
  togglePasswordVisibility,
  onSubmit,
  isLoading,
  errorMessage,
} = useLoginForm()
</script>

<template>
  <div class="w-full max-w-[420px] bg-white rounded-2xl shadow-xl shadow-slate-200/60 p-4.5 sm:p-8 md:p-9 border border-slate-100">
    <div class="flex flex-col items-center mb-5 sm:mb-6 text-center">
      <h1 class="text-xl sm:text-2xl font-bold text-slate-800 tracking-tight">{{ t('auth.loginTitle') }}</h1>
      <p class="text-xs sm:text-sm text-slate-400 mt-1">{{ t('auth.loginSubtitle') }}</p>
    </div>

    <div
      v-if="errorMessage"
      class="mb-4 p-3 rounded-lg bg-rose-50 border border-rose-200 text-rose-600 text-xs sm:text-sm flex items-center gap-2"
    >
      <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <circle cx="12" cy="12" r="10" />
        <line x1="12" y1="8" x2="12" y2="12" />
        <line x1="12" y1="16" x2="12.01" y2="16" />
      </svg>
      <span>{{ errorMessage }}</span>
    </div>

    <form @submit.prevent="onSubmit" class="space-y-4">
      <div>
        <div class="relative flex items-center">
          <span class="absolute left-3.5 text-slate-400 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
              <circle cx="12" cy="7" r="4" />
            </svg>
          </span>

          <Field
            name="email"
            type="text"
            :placeholder="t('auth.usernameOrEmail')"
            class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-slate-50 hover:bg-slate-100/80 focus:bg-white text-slate-800 placeholder-slate-400 text-sm rounded-xl border border-slate-200/80 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100 transition-all duration-200"
          />
        </div>
        <ErrorMessage name="email" class="text-rose-500 text-xs mt-1 block pl-1" />
      </div>

      <div>
        <div class="relative flex items-center">
          <span class="absolute left-3.5 text-slate-400 pointer-events-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
              <path d="M7 11V7a5 5 0 0 1 10 0v4" />
            </svg>
          </span>

          <Field
            name="password"
            :type="showPassword ? 'text' : 'password'"
            :placeholder="t('auth.passwordPlaceholder')"
            class="w-full pl-10 pr-10 py-2.5 sm:py-3 bg-slate-50 hover:bg-slate-100/80 focus:bg-white text-slate-800 placeholder-slate-400 text-sm rounded-xl border border-slate-200/80 focus:border-sky-400 focus:outline-none focus:ring-2 focus:ring-sky-100 transition-all duration-200"
          />

          <button
            type="button"
            @click="togglePasswordVisibility"
            class="absolute right-3.5 text-slate-400 hover:text-slate-600 cursor-pointer transition-colors"
            tabindex="-1"
          >
            <svg v-if="!showPassword" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
              <circle cx="12" cy="12" r="3" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="m15 18-.722-3.25" />
              <path d="M2 8a10.645 10.645 0 0 0 20 0" />
              <path d="m20 15-1.726-2.05" />
              <path d="m4 15 1.726-2.05" />
              <path d="m9 18 .722-3.25" />
            </svg>
          </button>
        </div>
        <ErrorMessage name="password" class="text-rose-500 text-xs mt-1 block pl-1" />
      </div>

      <div class="flex items-center justify-between text-xs sm:text-sm pt-1">
        <label class="group flex items-center gap-2.5 cursor-pointer select-none">
          <div class="relative flex items-center justify-center">
            <Field
              name="remember"
              type="checkbox"
              :value="true"
              :unchecked-value="false"
              class="peer sr-only"
            />
            <div
              class="w-4.5 h-4.5 rounded-md border border-slate-300 bg-white group-hover:border-sky-400 peer-checked:bg-sky-500 peer-checked:border-sky-500 peer-focus-visible:ring-2 peer-focus-visible:ring-sky-200 transition-all duration-200 shadow-xs flex items-center justify-center"
            ></div>
            <svg
              class="absolute w-3 h-3 text-white pointer-events-none opacity-0 peer-checked:opacity-100 transition-all duration-150 scale-75 peer-checked:scale-100"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="3"
              stroke-linecap="round"
              stroke-linejoin="round"
            >
              <polyline points="20 6 9 17 4 12" />
            </svg>
          </div>
          <span class="text-slate-600 group-hover:text-slate-800 transition-colors font-medium">
            {{ t('auth.rememberMe') }}
          </span>
        </label>

        <a
          href="#"
          class="text-sky-500 hover:text-sky-600 font-medium transition-colors hover:underline underline-offset-4 decoration-sky-400"
        >
          {{ t('auth.forgotPassword') }}
        </a>
      </div>

      <button
        type="submit"
        :disabled="isLoading"
        class="login-btn group w-full mt-2 py-3 px-4 bg-sky-400 hover:bg-sky-500 active:bg-sky-600 !text-white font-medium rounded-xl shadow-md shadow-sky-400/25 flex items-center justify-center gap-2 transition-all duration-200 cursor-pointer disabled:opacity-70 disabled:cursor-not-allowed text-sm sm:text-base"
      >
        <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <span class="!text-white font-medium">{{ t('auth.loginButton') }}</span>
        <svg
          v-if="!isLoading"
          xmlns="http://www.w3.org/2000/svg"
          class="w-4.5 h-4.5 transition-transform duration-200 ease-out group-hover:translate-x-1.5 !text-white text-white"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="2"
          stroke-linecap="round"
          stroke-linejoin="round"
        >
          <path d="M2 12h20" />
          <path d="m16 6 6 6-6 6" />
        </svg>
      </button>
    </form>
  </div>
</template>

<style scoped>
.login-btn,
.login-btn span,
.login-btn svg {
  color: #ffffff !important;
}
</style>
