import { ref } from 'vue'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import { loginSchema, type LoginFormData } from '../types'
import { useAuthStore } from '../stores/authStore'
import { useRouter } from 'vue-router'

export function useLoginForm() {
  const router = useRouter()
  const authStore = useAuthStore()
  const showPassword = ref(false)

  const form = useForm<LoginFormData>({
    validationSchema: toTypedSchema(loginSchema),
    initialValues: {
      email: '',
      password: '',
      remember: true,
    },
  })

  function togglePasswordVisibility() {
    showPassword.value = !showPassword.value
  }

  const onSubmit = form.handleSubmit(async (values) => {
    try {
      await authStore.login(values)
      router.push('/admin')
    } catch {
    }
  })

  return {
    form,
    showPassword,
    togglePasswordVisibility,
    onSubmit,
    isLoading: authStore.isLoading,
    errorMessage: authStore.errorMessage,
  }
}

