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
// src/types/index.ts
// src/types/index.ts

export interface Shipment {
  id: number
  shipment_code: string
  user_id: number
  user?: User
  consolidation_id?: number
  consolidation?: any
  description?: string
  weight: number
  weight_unit: string
  weight_kgs?: number
  seller_tracker_id?: string
  purchase_date?: string
  comments?: string
  // New foreign keys
  shipment_status_id?: number | null
  shipment_status?: { id: number; name: string }
  payment_method_id?: number | null
  payment_method?: { id: number; name: string }
  local_courier_id?: number | null
  local_courier?: { id: number; name: string }
  site_id?: number | null
  site?: { id: number; name: string }
  // Old fields kept for backward compatibility (or removed)
  // status?: string
  // payment_method?: string
  // local_delivery_by?: string
  // site_name?: string
  arrival_date?: string
  expected_delivery_date?: string
  date_delivered?: string
  item_value_pkr: number
  company_charges: number
  total: number
  received_amount?: number
  paid_by: string
  amount_due?: number
  receivable_cod?: number
  delivery_charges?: number
  images?: any[]
  created_at: string
  updated_at: string
}

// Add these helper interfaces if not already present
export interface Site {
  id: number
  name: string
  status: boolean
}

export interface ShipmentStatus {
  id: number
  name: string
  status: boolean
}

export interface PaymentMethod {
  id: number
  name: string
  status: boolean
}

export interface LocalCourier {
  id: number
  name: string
  status: boolean
}
