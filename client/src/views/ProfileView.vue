<template>
  <div class="container mt-5">
    <div class="row">
      <div class="col-md-8 offset-md-2">
        <div class="card">
          <div class="card-header">
            <h3>Profile</h3>
          </div>
          <div class="card-body">
            <form @submit.prevent="updateProfile" v-if="user">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input
                  type="text"
                  class="form-control"
                  id="name"
                  v-model="user.name"
                  required
                >
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="user.email"
                  required
                >
              </div>
              <div class="mb-3">
                <label for="newPassword" class="form-label">New Password (leave empty to keep current)</label>
                <input
                  type="password"
                  class="form-control"
                  id="newPassword"
                  v-model="newPassword"
                >
              </div>
              <div class="mb-3">
                <label for="confirmPassword" class="form-label">Confirm New Password</label>
                <input
                  type="password"
                  class="form-control"
                  id="confirmPassword"
                  v-model="confirmPassword"
                  :disabled="!newPassword"
                >
              </div>
              <div v-if="error" class="alert alert-danger">
                {{ error }}
              </div>
              <div v-if="success" class="alert alert-success">
                {{ success }}
              </div>
              <button type="submit" class="btn btn-primary" :disabled="loading">
                {{ loading ? 'Updating...' : 'Update Profile' }}
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import axios from 'axios'

export default {
  name: 'ProfileView',
  setup() {
    const authStore = useAuthStore()
    const user = ref(null)
    const newPassword = ref('')
    const confirmPassword = ref('')
    const error = ref('')
    const success = ref('')
    const loading = ref(false)

    onMounted(async () => {
      try {
        const response = await axios.get('/api/user')
        user.value = response.data
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

        await axios.put('/api/user', data)
        success.value = 'Profile updated successfully'
        newPassword.value = ''
        confirmPassword.value = ''
      } catch (err) {
        error.value = err.response?.data?.message || 'Failed to update profile'
      } finally {
        loading.value = false
      }
    }

    return {
      user,
      newPassword,
      confirmPassword,
      error,
      success,
      loading,
      updateProfile
    }
  }
}
</script>
