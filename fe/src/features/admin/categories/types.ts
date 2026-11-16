export interface Category {
  id: number
  parent_id: number | null
  code: string
  name: string
  slug: string
  description: string | null
  image: string | null
  sort_order: number
  is_active: boolean
  created_at?: string
  updated_at?: string
  deleted_at?: string | null
  parent?: Category | null
  children?: Category[]
}

export interface CategoryFormData {
  id?: number
  parent_id: number | null
  code: string
  name: string
  slug: string
  description: string | null
  image: string | null
  sort_order: number
  is_active: boolean
}

export interface CategoryFilterParams {
  search?: string
  parent_id?: number | string | null
  is_active?: boolean | string
  page?: number
  per_page?: number
  all?: boolean
  tree?: boolean
}

export interface CategoryStats {
  total: number
  active_count: number
  inactive_count: number
  root_count: number
  sub_count: number
}
