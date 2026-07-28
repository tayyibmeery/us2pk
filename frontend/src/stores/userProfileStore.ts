// frontend/src/stores/userProfileStore.ts
import { defineStore } from 'pinia'
import api from '@/services/api'
import { useAuthStore } from './authStore'

export interface UserProfile {
  id: number
  name: string
  email: string
  phone: string
  avatar: string
  address: string
  city_id: number
  city?: {
    id: number
    city_name: string
    city_code: string
  }
  pcode: string
  role: 'user' | 'admin'
  status: 'pending' | 'verified' | 'approved'
  bio?: string
  country?: string
  postal_code?: string
  tax_id?: string
  created_at?: string
}

export const useUserProfileStore = defineStore('userProfile', {
  state: () => ({
    profile: null as UserProfile | null,
    loading: false,
    error: null as string | null,
    avatarUploading: false,
    passwordUpdating: false,
  }),

  getters: {
    isProfileComplete: (state) => {
      if (!state.profile) return false
      const { name, email, phone, address, city_id, pcode } = state.profile
      return !!(name && email && phone && address && city_id && pcode)
    },
    fullName: (state) => state.profile?.name || '',
    userEmail: (state) => state.profile?.email || '',
    userAvatar: (state) => {
      if (!state.profile?.avatar) return '/images/user/owner.jpg'
      if (state.profile.avatar.startsWith('avatars/')) {
        return `${import.meta.env.VITE_BASE_URL || ''}/storage/${state.profile.avatar}`
      }
      return state.profile.avatar
    }
  },

  actions: {
    async fetchProfile() {
      console.log('🔵 Fetching profile...')
      this.loading = true
      this.error = null
      try {
        const authStore = useAuthStore()
        if (!authStore.isAuthenticated) {
          throw new Error('Not authenticated')
        }
        const response = await api.get('/user/profile')
        console.log('✅ Profile API response:', response.data)

        this.profile = response.data
        authStore.user = response.data

        console.log('✅ Profile set in store:', this.profile)
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to fetch profile'
        console.error('❌ Fetch profile error:', error)
        throw error
      } finally {
        this.loading = false
        console.log('🔵 Loading set to false')
      }
    },

    async updateProfile(data: Partial<UserProfile>) {
      this.loading = true
      this.error = null
      try {
        const response = await api.put('/user/profile', data)
        this.profile = response.data.user
        const authStore = useAuthStore()
        authStore.user = response.data.user
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to update profile'
        throw error
      } finally {
        this.loading = false
      }
    },

    async uploadAvatar(file: File) {
      this.avatarUploading = true
      this.error = null
      try {
        const formData = new FormData()
        formData.append('avatar', file)

        const response = await api.post('/user/avatar', formData, {
          headers: { 'Content-Type': 'multipart/form-data' }
        })

        await this.fetchProfile()
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to upload avatar'
        console.error('Upload avatar error:', error)
        throw error
      } finally {
        this.avatarUploading = false
      }
    },

    async changePassword(currentPassword: string, newPassword: string, newPasswordConfirmation: string) {
      this.passwordUpdating = true
      this.error = null
      try {
        const response = await api.post('/user/change-password', {
          current_password: currentPassword,
          new_password: newPassword,
          new_password_confirmation: newPasswordConfirmation
        })
        return response.data
      } catch (error: any) {
        this.error = error.response?.data?.message || 'Failed to change password'
        throw error
      } finally {
        this.passwordUpdating = false
      }
    },

    reset() {
      this.profile = null
      this.loading = false
      this.error = null
      this.avatarUploading = false
      this.passwordUpdating = false
    }
  }
})

// ✅ Also export as default for compatibility
export default useUserProfileStore
