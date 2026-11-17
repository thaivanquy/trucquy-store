export function toSlug(text: string): string {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[[\u0300-\u036f]/g, '')
    .replace(/[[đĐ]/g, 'd')
    .replace(/[[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-')
    .replace(/-+/g, '-')
}

export function generateAbbr(name: string): string {
  const clean = name
    .normalize('NFD')
    .replace(/[[\u0300-\u036f]/g, '')
    .replace(/[[đĐ]/g, 'd')
    .replace(/[[^a-zA-Z0-9\s]/g, '')
    .trim()

  const words = clean.split(/\s+/).filter(Boolean)

  if (words.length === 0) return 'CAT'
  const firstWord = words[0]
  if (words.length === 1 && firstWord) return firstWord.substring(0, 4).toUpperCase()

  return words
    .map((w) => w.charAt(0).toUpperCase())
    .join('')
    .substring(0, 5)
}

export function generateCategoryCode(
  name: string,
  parentId?: number | null,
  categories: { id: number; code: string; parent_id: number | null }[] = []
): string {
  if (parentId) {
    const parent = categories.find((c) => c.id === parentId)
    const parentPrefix = parent?.code || `DM-${parentId}`
    const siblings = categories.filter((c) => c.parent_id === parentId)
    let counter = siblings.length + 1
    let code = `${parentPrefix}-${String(counter).padStart(2, '0')}`

    while (categories.some((c) => c.code === code)) {
      counter++
      code = `${parentPrefix}-${String(counter).padStart(2, '0')}`
    }

    return code.toUpperCase()
  }

  const abbr = generateAbbr(name)
  const baseCode = `DM-${abbr}`
  let code = baseCode
  let counter = 1

  while (categories.some((c) => c.code === code)) {
    code = `${baseCode}-${String(counter).padStart(2, '0')}`
    counter++
  }

  return code.toUpperCase()
}

export function generateUnitCode(
  name: string,
  units: { id: number; code: string }[] = []
): string {
  const abbr = generateAbbr(name)
  const baseCode = `DVT-${abbr}`
  let code = baseCode
  let counter = 1

  while (units.some((u) => u.code === code)) {
    code = `${baseCode}-${String(counter).padStart(2, '0')}`
    counter++
  }

  return code.toUpperCase()
}

export function generateBarcode(prefix: string = '200'): string {
  const timestamp = Date.now().toString().slice(-7)
  const randomPart = Math.floor(Math.random() * 100).toString().padStart(2, '0')
  let code = `${prefix}${timestamp}${randomPart}`
  code = code.padEnd(12, '0').slice(0, 12)

  let sum = 0
  for (let i = 0; i < 12; i++) {
    const digit = parseInt(code[i] ?? '0', 10)
    sum += i % 2 === 0 ? digit : digit * 3
  }
  const checksum = (10 - (sum % 10)) % 10
  return `${code}${checksum}`
}
