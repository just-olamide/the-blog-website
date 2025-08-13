<template>
  <div class="profile-page">
    <div class="container py-5">
      <div class="profile-card">
        <div class="profile-header">
          <div class="avatar">
            <img v-if="user?.avatar" :src="avatarUrl" :alt="user?.name || 'User'" />
            <div v-else class="avatar-placeholder">{{ (user?.name || 'U').charAt(0).toUpperCase() }}</div>
          </div>
          <div class="identity">
            <h1 class="name">{{ user?.name || '—' }}</h1>
            <p class="username">{{ user?.username || '' }}</p>
            <div class="meta">
              <span class="joined">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M8 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <path d="M16 2V5" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                  <rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="2"/>
                  <path d="M3 10H21" stroke="currentColor" stroke-width="2"/>
                </svg>
                Joined {{ joinedAt }}
              </span>
              <span class="dot">•</span>
              <span class="email">{{ user?.email }}</span>
            </div>
          </div>
          <RouterLink to="/profile/edit" class="edit-btn">
            <svg class="edit-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M12 20H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
              <path d="M16.5 3.5L20.5 7.5L8 20H4V16L16.5 3.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Edit Profile
          </RouterLink>
        </div>

        <hr class="divider" />

        <div class="stats">
          <div class="stat">
            <div class="value">{{ stats.posts }}</div>
            <div class="label">Posts</div>
          </div>
          <div class="stat">
            <div class="value">{{ stats.likes }}</div>
            <div class="label">Total Likes</div>
          </div>
          <div class="stat">
            <div class="value">{{ stats.comments }}</div>
            <div class="label">Total Comments</div>
          </div>
        </div>
      </div>
    </div>
  </div>
 </template>

<script>
import { ref, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from '@/plugins/axios'

export default {
  name: 'ProfileView',
  setup() {
    const user = ref(null)
    const stats = ref({ posts: 0, likes: 0, comments: 0 })
    const newPassword = ref('')
    const confirmPassword = ref('')
    const error = ref('')
    const success = ref('')
    const loading = ref(false)

    onMounted(async () => {
      try {
        const [userRes, statsRes] = await Promise.all([
          axios.get('/user'),
          axios.get('/profile/stats'),
        ])
        user.value = userRes.data
        stats.value = statsRes.data
      } catch (err) {
        error.value = 'Failed to load profile'
      }
    })

    const updateProfile = async () => {
      error.value = ''
      success.value = ''

      if (newPassword.value && newPassword.value !== confirmPassword.value) {
        error.value = 'Passwords do not match'
        return
      }

      loading.value = true
      try {
        const data = {
          name: user.value.name,
          email: user.value.email
        }

        if (newPassword.value) {
          data.password = newPassword.value
          data.password_confirmation = confirmPassword.value
        }

        await axios.put('/profile', data)
        success.value = 'Profile updated successfully'
        newPassword.value = ''
        confirmPassword.value = ''
      } catch (err) {
        error.value = err.response?.data?.message || 'Failed to update profile'
      } finally {
        loading.value = false
      }
    }

    const avatarUrl = computed(() => (user.value?.avatar ? `http://localhost:8000/storage/${user.value.avatar}` : ''))
    const joinedAt = computed(() => {
      if (!user.value?.created_at) return ''
      const d = new Date(user.value.created_at)
      return d.toLocaleString('en-US', { month: 'long', year: 'numeric' })
    })

    return {
      user,
      stats,
      newPassword,
      confirmPassword,
      error,
      success,
      loading,
      updateProfile,
      avatarUrl,
      joinedAt
    }
  }
}
</script>

<style scoped>
.profile-page {
  background-color: #f8fafc;
  min-height: calc(100vh - 80px);
}

.profile-card {
  background: white;
  border-radius: 16px;
  border: 1px solid rgba(0,0,0,0.06);
  box-shadow: 0 2px 6px rgba(0,0,0,0.06);
  padding: 24px;
  max-width: 900px;
  margin: 0 auto;
}

.profile-header {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 20px;
  align-items: center;
}

.avatar {
  width: 96px;
  height: 96px;
  border-radius: 50%;
  overflow: hidden;
}

.avatar img { width: 100%; height: 100%; object-fit: cover; }

.avatar-placeholder {
  width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
  background: linear-gradient(135deg, #667eea 0%, #8b5cf6 100%);
  color: white; font-weight: 700; font-size: 36px;
}

.identity .name { margin: 0; font-weight: 700; color: #0f172a; font-size: 34px; }
.identity .username { margin: 4px 0 0; color: #64748b; }
.meta { display: flex; align-items: center; gap: 12px; margin-top: 10px; color: #64748b; }
.dot { color: #cbd5e1; }

.edit-btn {
  display: inline-flex; align-items: center; gap: 8px;
  border: 1px solid #e2e8f0; background: #f8fafc; color: #0f172a;
  padding: 10px 14px; border-radius: 10px; text-decoration: none;
  transition: all .2s ease;
}
.edit-btn:hover { background: #eef2ff; border-color: #c7d2fe; }

.divider { border: none; border-top: 1px solid #e2e8f0; margin: 18px 0; }

.stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; text-align: center; }
.stat .value { font-size: 28px; font-weight: 700; color: #0f172a; }
.stat .label { color: #64748b; }

@media (max-width: 640px) {
  .profile-header { grid-template-columns: 1fr; text-align: center; }
  .edit-btn { justify-self: center; }
}
</style>
