// src/types/index.ts
export interface City {
  id: number
  city_name: string
  city_code: string
  status: boolean
  created_at?: string
  updated_at?: string
}

export interface Category {
  id: number
  name: string
  description: string | null
  status: 'Active' | 'Inactive'
  created_at?: string
  updated_at?: string
}

export interface SubCategory {
  id: number
  name: string
  description: string | null
  category_id: number
  category?: Category
  status: 'Active' | 'Inactive'
  created_at?: string
  updated_at?: string
}

export interface SubSubCategory {
  id: number
  name: string
  description: string | null
  category_id: number
  sub_category_id: number
  category?: Category
  sub_category?: SubCategory
  status: 'Active' | 'Inactive'
  created_at?: string
  updated_at?: string
}

// Paginated response from Laravel
export interface PaginatedResponse<T> {
  current_page: number
  data: T[]
  first_page_url: string
  from: number
  last_page: number
  last_page_url: string
  links: any[]
  next_page_url: string | null
  path: string
  per_page: number
  prev_page_url: string | null
  to: number
  total: number
}
