import { message } from 'ant-design-vue'

export function formatPrice(val?: number | string | null): string {
  const num = Number(val) || 0
  return new Intl.NumberFormat('vi-VN', {
    style: 'currency',
    currency: 'VND',
    maximumFractionDigits: 0,
  }).format(num)
}

export function formatNumber(val?: number | string | null): string {
  const num = Number(val) || 0
  return new Intl.NumberFormat('vi-VN').format(num)
}

export function priceInputFormatter(val: number | string | undefined | null): string {
  if (val === undefined || val === null || val === '') return ''
  return `${val}`.replace(/\B(?=(\d{3})+(?!\d))/g, ',')
}

export function priceInputParser(val: string): string {
  if (!val) return ''
  return val.replace(/\$\s?|(,*)/g, '')
}

export function calculateMargin(
  sellingPrice?: number | null,
  costPrice?: number | null
): number | null {
  const sell = Number(sellingPrice) || 0
  const cost = Number(costPrice) || 0
  if (cost <= 0 || sell <= 0) return null
  return Math.round(((sell - cost) / cost) * 100)
}

export async function copyToClipboard(
  text?: string | null,
  successMsg: string = 'Đã sao chép vào bộ nhớ tạm!'
): Promise<boolean> {
  if (!text) return false
  try {
    if (navigator?.clipboard?.writeText) {
      await navigator.clipboard.writeText(text)
    } else {
      const textarea = document.createElement('textarea')
      textarea.value = text
      textarea.style.position = 'fixed'
      textarea.style.opacity = '0'
      document.body.appendChild(textarea)
      textarea.select()
      document.execCommand('copy')
      document.body.removeChild(textarea)
    }
    message.success(successMsg)
    return true
  } catch {
    message.error('Không thể sao chép văn bản')
    return false
  }
}
