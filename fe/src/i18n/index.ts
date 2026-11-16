import { ref, type App, type InjectionKey, inject } from 'vue'
import vi from './locales/vi'
import en from './locales/en'

export type Locale = 'VI' | 'EN'

const messages: Record<Locale, Record<string, any>> = {
  VI: vi,
  EN: en,
}

function getStoredLocale(): Locale {
  try {
    const saved = localStorage.getItem('trucquy_locale')
    if (saved && (saved.toUpperCase() === 'EN' || saved.toUpperCase() === 'VI')) {
      return saved.toUpperCase() as Locale
    }
  } catch {
  }
  return 'VI'
}

const currentLocale = ref<Locale>(getStoredLocale())

function resolveKey(obj: Record<string, any>, path: string): string | undefined {
  const segments = path.split('.')
  let current: any = obj
  for (const seg of segments) {
    if (current && typeof current === 'object' && seg in current) {
      current = current[seg]
    } else {
      return undefined
    }
  }
  return typeof current === 'string' ? current : undefined
}

export function t(key: string, params?: Record<string, string | number>): string {
  const locale = currentLocale.value
  let text = resolveKey(messages[locale], key)

  if (text === undefined && locale !== 'VI') {
    text = resolveKey(messages.VI, key)
  }

  if (text === undefined) {
    return key
  }

  if (params) {
    for (const [pKey, pVal] of Object.entries(params)) {
      text = text.replace(new RegExp(`{${pKey}}`, 'g'), String(pVal))
    }
  }

  return text
}

export function setLocale(code: string) {
  const formatted = code.toUpperCase() as Locale
  if (formatted === 'VI' || formatted === 'EN') {
    currentLocale.value = formatted
    try {
      localStorage.setItem('trucquy_locale', formatted)
    } catch {
    }
    if (typeof document !== 'undefined') {
      document.documentElement.lang = formatted.toLowerCase()
    }
  }
}

export interface I18nInstance {
  locale: typeof currentLocale
  t: typeof t
  setLocale: typeof setLocale
}

const I18nSymbol: InjectionKey<I18nInstance> = Symbol('trucquy_i18n')

export function useI18n(): I18nInstance {
  const instance = inject(I18nSymbol, null)
  if (instance) return instance
  return {
    locale: currentLocale,
    t,
    setLocale,
  }
}

export const i18n = {
  install(app: App) {
    const instance: I18nInstance = {
      locale: currentLocale,
      t,
      setLocale,
    }
    app.provide(I18nSymbol, instance)
    app.config.globalProperties.$t = t

    if (typeof document !== 'undefined') {
      document.documentElement.lang = currentLocale.value.toLowerCase()
    }
  },
}

export default i18n
