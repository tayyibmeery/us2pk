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


// frontend/src/types/index.ts

export interface User {
  id: number
  name: string
  email: string
  avatar?: string
  phone: string
  address: string
  city_id: number
  city?: City
  pcode: string
  source?: string
  status: 'pending' | 'verified' | 'approved'
  role: 'user' | 'admin'
  created_at: string
  updated_at: string
}
// frontend/src/types/index.ts
export interface Shipment {
  id: number
  pcode: string
  user_id: number
  user?: User
  consolidation_id?: number
  consolidation?: any
  description?: string
  weight: number
  weight_unit: string
  weight_kgs?: number
  seller_tracker_id?: string
  site_name?: string
  purchase_date?: string
  comments?: string
  status: string
  arrival_date?: string
  expected_delivery_date?: string
  date_delivered?: string
  item_value_pkr: number
  company_charges: number
  total: number
  received_amount?: number
  paid_by: string
  amount_due?: number
  payment_method?: string
  receivable_cod?: number
  local_delivery_by?: string
  delivery_charges?: number
  images?: any[]
  created_at: string
  updated_at: string
}
