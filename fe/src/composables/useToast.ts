import { message } from 'ant-design-vue'

export function useToast() {
  function show(type: 'success' | 'error' | 'warning' | 'info', content: string, duration = 3) {
    let hide: (() => void) | null = null

    hide = message[type]({
      content,
      duration,
      onClick: () => {
        if (typeof hide === 'function') {
          hide()
        } else {
          message.destroy()
        }
      },
    })

    return hide
  }

  function success(content: string, duration = 3) {
    return show('success', content, duration)
  }

  function error(content: string, duration = 3) {
    return show('error', content, duration)
  }

  function warning(content: string, duration = 3) {
    return show('warning', content, duration)
  }

  function info(content: string, duration = 3) {
    return show('info', content, duration)
  }

  return {
    success,
    error,
    warning,
    info,
  }
}
