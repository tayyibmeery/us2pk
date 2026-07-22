// frontend/src/types/page.ts
export interface Page {
  id: number;
  title: string;
  slug: string;
  type: 'page' | 'hero' | 'service' | 'testimonial' | 'team' | 'pricing' | 'faq' | 'blog' | 'about' | 'whyus' | 'contact';
  content: string;
  image: string | null;
  icon: string | null;
  meta: Record<string, any>;
  order: number;
  status: boolean;
  parent_id: number | null;
  created_at: string;
  updated_at: string;
  deleted_at: string | null;
}

export interface PageFormData {
  title: string;
  slug: string;
  type: string;
  content: string;
  status: boolean;
  order: number;
  image: string | null;
  icon: string | null;
  meta: Record<string, any>;
  parent_id: number | null;
}

export interface PageFilters {
  type: string;
  status: boolean | null;
  search: string;
  order_by: string;
  order_dir: 'asc' | 'desc';
  per_page: number;
}
