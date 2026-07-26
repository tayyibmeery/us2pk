// src/types/index.ts
export interface City {
  id: number
  city_name: string
  city_code: string
  status: boolean
  created_at?: string
  updated_at?: string
}

export interface Page {
  id: number;
  title: string;
  slug: string;
  type: string;
  content: string;
  status: boolean;
  order: number;
  image: string | null;
  icon: string | null;
  meta: any;
  parent_id: number | null;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
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
  shipment_status_id?: number | null
  shipment_status?: { id: number; name: string }
  payment_method_id?: number | null
  payment_method?: { id: number; name: string }
  local_courier_id?: number | null
  local_courier?: { id: number; name: string }
  site_id?: number | null
  site?: { id: number; name: string }
  arrival_date?: string
  expected_delivery_date?: string
  date_delivered?: string
  item_value_pkr: number
  company_charges: number
  total: number
  received_amount?: number
  bought_by: string
  amount_due?: number
  receivable_cod?: number
  delivery_charges?: number
  images?: any[]
  created_at: string
  updated_at: string
}

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
