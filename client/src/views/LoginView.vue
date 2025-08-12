<script>
import { useAuthStore } from '@/stores/auth'

export default {
  name: 'LoginView',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      error: null,
      loading: false
    }
  },
  methods: {
    async handleSubmit() {
      this.loading = true
      this.error = null
      const authStore = useAuthStore()

      try {
        await authStore.login(this.form)
        this.$router.push('/dashboard')
      } catch (err) {
        this.error = err.response?.data?.message || 'An error occurred during login'
      } finally {
        this.loading = false
      }
    }
  }
}
</script>

<template>
  <div class="login-page">
    <div class="card">
      <div class="card-body">
        <h2 class="text-center mb-4">Login</h2>
        
        <div v-if="error" class="alert alert-danger">
          {{ error }}
        </div>

        <form @submit.prevent="handleSubmit">
          <div class="mb-3">
            <label for="email" class="form-label">Email address</label>
            <input
              type="email"
              class="form-control"
              id="email"
              v-model="form.email"
              required
            >
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              type="password"
              class="form-control"
              id="password"
              v-model="form.password"
              required
            >
          </div>

          <button
            type="submit"
            class="btn btn-primary w-100"
            :disabled="loading"
          >
            {{ loading ? 'Logging in...' : 'Login' }}
          </button>
        </form>

        <p class="text-center mt-3">
          Don't have an account?
          <RouterLink to="/register">Register</RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>

<style scoped>
.login-page {
  max-width: 400px;
  margin: 40px auto;
  padding: 0 15px;
}

.card {
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

.card-body {
  padding: 30px;
}
</style>
